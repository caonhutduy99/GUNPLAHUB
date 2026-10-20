<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    private const LOW_STOCK = 5;

    private const DELIVERED_AT = 'COALESCE(orders.delivered_at, orders.order_date)';

    // ---------------- Doanh thu ----------------

    public function revenue(Request $request)
    {
        [$from, $to, $period] = $this->resolveRange($request);

        return view('admin.reports.revenue', $this->revenueData($from, $to, $period) + [
            'from' => $from,
            'to' => $to,
            'period' => $period,
        ]);
    }

    public function exportRevenue(Request $request): StreamedResponse
    {
        [$from, $to, $period] = $this->resolveRange($request);
        $data = $this->revenueData($from, $to, $period);
        $periodLabel = ['day' => 'Ngày', 'month' => 'Tháng', 'year' => 'Năm'][$period];

        return $this->csv('doanh-thu-' . $from->format('Ymd') . '-' . $to->format('Ymd') . '.csv', function ($out) use ($data, $from, $to, $periodLabel) {
            fputcsv($out, ['BÁO CÁO DOANH THU'], ';');
            fputcsv($out, ['Từ ngày', $from->format('d/m/Y'), 'Đến ngày', $to->format('d/m/Y')], ';');
            fputcsv($out, [], ';');

            fputcsv($out, [$periodLabel, 'Số đơn', 'Doanh thu (đ)'], ';');
            foreach ($data['series'] as $row) {
                fputcsv($out, [$row['label'], $row['orders'], (int) $row['revenue']], ';');
            }
            fputcsv($out, ['Tổng', $data['totalOrders'], (int) $data['totalRevenue']], ';');

            fputcsv($out, [], ';');
            fputcsv($out, ['SẢN PHẨM BÁN CHẠY'], ';');
            fputcsv($out, ['SKU', 'Tên sản phẩm', 'Số lượng bán', 'Doanh thu (đ)'], ';');
            foreach ($data['bestSellers'] as $item) {
                fputcsv($out, [$item->sku, $item->name, (int) $item->qty, (int) $item->revenue], ';');
            }
        });
    }

    private function revenueData(Carbon $from, Carbon $to, string $period): array
    {
        $sqlFormat = ['day' => '%Y-%m-%d', 'month' => '%Y-%m', 'year' => '%Y'][$period];
        $phpFormat = ['day' => 'Y-m-d', 'month' => 'Y-m', 'year' => 'Y'][$period];
        $labelFormat = ['day' => 'd/m', 'month' => 'm/Y', 'year' => 'Y'][$period];

        $rows = Order::revenue()->deliveredBetween($from, $to)
            ->selectRaw("DATE_FORMAT(" . self::DELIVERED_AT . ", '{$sqlFormat}') as period_key, COUNT(*) as orders, SUM(orders.total_amount) as revenue")
            ->groupBy('period_key')
            ->orderBy('period_key')
            ->get()
            ->keyBy('period_key');

        // Lấp đầy các kỳ không có đơn để biểu đồ liên tục
        $series = [];
        $cursor = $from->copy();
        if ($period === 'month') {
            $cursor = $cursor->startOfMonth();
        } elseif ($period === 'year') {
            $cursor = $cursor->startOfYear();
        }

        for ($i = 0; $cursor->lte($to) && $i < 400; $i++) {
            $key = $cursor->format($phpFormat);
            $row = $rows->get($key);
            $series[] = [
                'key' => $key,
                'label' => $cursor->format($labelFormat),
                'orders' => (int) ($row->orders ?? 0),
                'revenue' => (float) ($row->revenue ?? 0),
            ];

            $cursor = match ($period) {
                'day' => $cursor->copy()->addDay(),
                'month' => $cursor->copy()->addMonthNoOverflow(),
                default => $cursor->copy()->addYear(),
            };
        }

        $bestSellers = OrderDetail::query()
            ->join('orders', 'orders.id', '=', 'order_details.order_id')
            ->join('products', 'products.sku', '=', 'order_details.product_sku')
            ->whereIn('orders.status', Order::REVENUE_STATUSES)
            ->whereBetween(DB::raw(self::DELIVERED_AT), [$from, $to])
            ->selectRaw('products.sku as sku, products.name as name, SUM(order_details.quantity) as qty, SUM(order_details.quantity * order_details.unit_price) as revenue')
            ->groupBy('products.sku', 'products.name')
            ->orderByDesc('qty')
            ->orderBy('products.name')
            ->limit(10)
            ->get();

        $statusCounts = Order::whereBetween('order_date', [$from, $to])
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->orderByDesc('total')
            ->get();

        $totalRevenue = collect($series)->sum('revenue');
        $totalOrders = collect($series)->sum('orders');

        return [
            'series' => $series,
            'totalRevenue' => $totalRevenue,
            'totalOrders' => $totalOrders,
            'average' => $totalOrders > 0 ? $totalRevenue / $totalOrders : 0,
            'bestSellers' => $bestSellers,
            'statusCounts' => $statusCounts,
        ];
    }

    private function resolveRange(Request $request): array
    {
        $period = in_array($request->input('period'), ['day', 'month', 'year'], true)
            ? $request->input('period')
            : 'day';

        $to = $this->parseDate($request->input('to')) ?? Carbon::today();
        $from = $this->parseDate($request->input('from')) ?? match ($period) {
            'day' => $to->copy()->subDays(29),
            'month' => $to->copy()->subMonths(11)->startOfMonth(),
            default => $to->copy()->subYears(4)->startOfYear(),
        };

        if ($from->gt($to)) {
            [$from, $to] = [$to->copy(), $from->copy()];
        }
        // Giới hạn khoảng xem để biểu đồ không quá dài
        if ($period === 'day' && $from->diffInDays($to) > 365) {
            $from = $to->copy()->subDays(365);
        }

        return [$from->copy()->startOfDay(), $to->copy()->endOfDay(), $period];
    }

    // ---------------- Tồn kho ----------------

    public function inventory()
    {
        $active = Product::where('status', '!=', 'Ngừng kinh doanh');

        return view('admin.reports.inventory', [
            'products' => (clone $active)->with(['brand', 'category'])->orderBy('stock_quantity')->orderBy('sku')->paginate(20),
            'totalSkus' => (clone $active)->count(),
            'totalUnits' => (int) (clone $active)->sum('stock_quantity'),
            'totalValue' => (float) (clone $active)->sum(DB::raw('price * stock_quantity')),
            'lowStockCount' => (clone $active)->where('stock_quantity', '<=', self::LOW_STOCK)->count(),
            'lowStock' => self::LOW_STOCK,
        ]);
    }

    public function exportInventory(): StreamedResponse
    {
        return $this->csv('ton-kho-' . now()->format('Ymd') . '.csv', function ($out) {
            fputcsv($out, ['BÁO CÁO TỒN KHO', 'Ngày', now()->format('d/m/Y H:i')], ';');
            fputcsv($out, [], ';');
            fputcsv($out, ['SKU', 'Tên sản phẩm', 'Thương hiệu', 'Cấp độ / Loại', 'Giá (đ)', 'Tồn kho', 'Giá trị tồn (đ)', 'Trạng thái'], ';');

            Product::with(['brand', 'category'])
                ->where('status', '!=', 'Ngừng kinh doanh')
                ->orderBy('stock_quantity')->orderBy('sku')
                ->chunk(200, function ($products) use ($out) {
                    foreach ($products as $p) {
                        fputcsv($out, [
                            $p->sku, $p->name, $p->brand?->name, $p->category?->short_name,
                            (int) $p->price, $p->stock_quantity, (int) ($p->price * $p->stock_quantity), $p->status,
                        ], ';');
                    }
                });
        });
    }

    // ---------------- Hàm phụ ----------------

    /** Xuất CSV mở được bằng Excel (có BOM UTF-8 và dòng sep=; để Excel tách cột đúng) */
    private function csv(string $filename, callable $write): StreamedResponse
    {
        return response()->streamDownload(function () use ($write) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fwrite($out, "sep=;\r\n");
            $write($out);
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function parseDate(?string $value): ?Carbon
    {
        try {
            return $value ? Carbon::parse($value) : null;
        } catch (\Throwable) {
            return null;
        }
    }
}

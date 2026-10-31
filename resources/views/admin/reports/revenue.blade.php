@extends('layouts.app')

@section('title', 'Báo cáo doanh thu - GunplaHub')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <h3 class="mb-0">Báo cáo doanh thu</h3>
    <div class="no-print d-flex gap-2">
        <a href="{{ route('admin.reports.revenue.export', request()->query()) }}" class="btn btn-outline-success btn-sm">
            <x-icon name="sheet" class="me-1" />Xuất Excel
        </a>
        <button type="button" class="btn btn-outline-dark btn-sm" onclick="window.print()">
            <x-icon name="printer" class="me-1" />In / Lưu PDF
        </button>
    </div>
</div>

<ul class="nav nav-pills mb-3 no-print">
    <li class="nav-item"><a class="nav-link active" href="{{ route('admin.reports.revenue') }}">Doanh thu</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.reports.inventory') }}">Tồn kho</a></li>
</ul>

<form method="GET" action="{{ route('admin.reports.revenue') }}" class="row g-2 mb-3 no-print">
    <div class="col-md-3">
        <select name="period" class="form-select">
            <option value="day" @selected($period === 'day')>Theo ngày</option>
            <option value="month" @selected($period === 'month')>Theo tháng</option>
            <option value="year" @selected($period === 'year')>Theo năm</option>
        </select>
    </div>
    <div class="col-md-3">
        <input type="date" name="from" value="{{ $from->format('Y-m-d') }}" class="form-control">
    </div>
    <div class="col-md-3">
        <input type="date" name="to" value="{{ $to->format('Y-m-d') }}" class="form-control">
    </div>
    <div class="col-md-3">
        <button type="submit" class="btn btn-secondary w-100">Xem báo cáo</button>
    </div>
</form>

<p class="text-muted small">
    Từ {{ $from->format('d/m/Y') }} đến {{ $to->format('d/m/Y') }}. Doanh thu tính theo các đơn đã giao thành công.
</p>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card text-center shadow-sm"><div class="card-body">
            <div class="fs-3 text-danger fw-semibold">{{ \App\Support\Money::format($totalRevenue) }}</div>
            <div class="text-muted">Tổng doanh thu</div>
        </div></div>
    </div>
    <div class="col-md-4">
        <div class="card text-center shadow-sm"><div class="card-body">
            <div class="fs-3 fw-semibold">{{ $totalOrders }}</div>
            <div class="text-muted">Đơn giao thành công</div>
        </div></div>
    </div>
    <div class="col-md-4">
        <div class="card text-center shadow-sm"><div class="card-body">
            <div class="fs-3 fw-semibold">{{ \App\Support\Money::format($average) }}</div>
            <div class="text-muted">Giá trị trung bình mỗi đơn</div>
        </div></div>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <canvas id="revenueChart" height="110"></canvas>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header fw-semibold">Doanh thu theo {{ ['day' => 'ngày', 'month' => 'tháng', 'year' => 'năm'][$period] }}</div>
            <div class="table-responsive" style="max-height: 420px; overflow-y: auto;">
                <table class="table table-sm table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr><th>Kỳ</th><th class="text-center">Số đơn</th><th class="text-end">Doanh thu</th></tr>
                    </thead>
                    <tbody>
                        @foreach($series as $row)
                            <tr>
                                <td>{{ $row['label'] }}</td>
                                <td class="text-center">{{ $row['orders'] }}</td>
                                <td class="text-end">{{ \App\Support\Money::format($row['revenue']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold">
                            <td>Tổng</td>
                            <td class="text-center">{{ $totalOrders }}</td>
                            <td class="text-end">{{ \App\Support\Money::format($totalRevenue) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card shadow-sm mb-4">
            <div class="card-header fw-semibold">Sản phẩm bán chạy (top 10)</div>
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr><th>Sản phẩm</th><th class="text-center">Đã bán</th><th class="text-end">Doanh thu</th></tr>
                    </thead>
                    <tbody>
                        @forelse($bestSellers as $item)
                            <tr>
                                <td>
                                    {{ $item->name }}
                                    <div class="small text-muted">{{ $item->sku }}</div>
                                </td>
                                <td class="text-center">{{ $item->qty }}</td>
                                <td class="text-end">{{ \App\Support\Money::format($item->revenue) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted py-3">Chưa có dữ liệu trong khoảng này.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header fw-semibold">Đơn hàng theo trạng thái (theo ngày đặt)</div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <tbody>
                        @forelse($statusCounts as $row)
                            <tr><td>{{ $row->status }}</td><td class="text-end">{{ $row->total }}</td></tr>
                        @empty
                            <tr><td class="text-center text-muted py-3">Chưa có đơn nào trong khoảng này.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
    (function () {
        var series = @json($series);
        var canvas = document.getElementById('revenueChart');
        if (!canvas || typeof Chart === 'undefined') { return; }

        new Chart(canvas, {
            data: {
                labels: series.map(function (r) { return r.label; }),
                datasets: [
                    {
                        type: 'bar',
                        label: 'Doanh thu (đ)',
                        data: series.map(function (r) { return r.revenue; }),
                        backgroundColor: 'rgba(232, 80, 30, 0.75)',
                        yAxisID: 'y'
                    },
                    {
                        type: 'line',
                        label: 'Số đơn',
                        data: series.map(function (r) { return r.orders; }),
                        borderColor: '#0d6efd',
                        backgroundColor: '#0d6efd',
                        tension: 0.25,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                interaction: { mode: 'index', intersect: false },
                scales: {
                    y: { beginAtZero: true, position: 'left', title: { display: true, text: 'Doanh thu (đ)' } },
                    y1: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false }, ticks: { precision: 0 }, title: { display: true, text: 'Số đơn' } }
                }
            }
        });
    })();
</script>
@endpush

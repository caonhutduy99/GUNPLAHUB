@extends('layouts.app')

@section('title', 'Báo cáo tồn kho - GunplaHub')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <h3 class="mb-0">Báo cáo tồn kho</h3>
    <div class="no-print d-flex gap-2">
        <a href="{{ route('admin.reports.inventory.export') }}" class="btn btn-outline-success btn-sm">
            <i class="bi bi-file-earmark-excel me-1"></i>Xuất Excel
        </a>
        <button type="button" class="btn btn-outline-dark btn-sm" onclick="window.print()">
            <i class="bi bi-printer me-1"></i>In / Lưu PDF
        </button>
    </div>
</div>

<ul class="nav nav-pills mb-3 no-print">
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.reports.revenue') }}">Doanh thu</a></li>
    <li class="nav-item"><a class="nav-link active" href="{{ route('admin.reports.inventory') }}">Tồn kho</a></li>
</ul>

<p class="text-muted small">Tính đến {{ now()->format('d/m/Y H:i') }}, không gồm sản phẩm đã ngừng kinh doanh. Sắp xếp từ tồn kho thấp đến cao.</p>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card text-center shadow-sm"><div class="card-body">
            <div class="fs-3 fw-semibold">{{ $totalSkus }}</div><div class="text-muted">Mã sản phẩm</div>
        </div></div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center shadow-sm"><div class="card-body">
            <div class="fs-3 fw-semibold">{{ number_format($totalUnits, 0, ',', '.') }}</div><div class="text-muted">Tổng số lượng tồn</div>
        </div></div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center shadow-sm"><div class="card-body">
            <div class="fs-4 fw-semibold">{{ \App\Support\Money::format($totalValue) }}</div><div class="text-muted">Giá trị tồn kho</div>
        </div></div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center shadow-sm {{ $lowStockCount > 0 ? 'border-danger' : '' }}"><div class="card-body">
            <div class="fs-3 fw-semibold {{ $lowStockCount > 0 ? 'text-danger' : '' }}">{{ $lowStockCount }}</div>
            <div class="text-muted">Sắp hết (≤ {{ $lowStock }})</div>
        </div></div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>SKU</th>
                    <th>Sản phẩm</th>
                    <th>Thương hiệu</th>
                    <th>Cấp độ / Loại</th>
                    <th class="text-end">Giá</th>
                    <th class="text-center">Tồn</th>
                    <th class="text-end">Giá trị tồn</th>
                    <th>Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                    <tr class="{{ $product->stock_quantity <= $lowStock ? 'table-warning' : '' }}">
                        <td><code>{{ $product->sku }}</code></td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->brand?->name ?? '—' }}</td>
                        <td>{{ $product->category->short_name }}</td>
                        <td class="text-end">{{ \App\Support\Money::format($product->price) }}</td>
                        <td class="text-center fw-semibold">{{ $product->stock_quantity }}</td>
                        <td class="text-end">{{ \App\Support\Money::format($product->price * $product->stock_quantity) }}</td>
                        <td>{{ $product->status }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3 no-print">
    {{ $products->links('pagination::bootstrap-5') }}
</div>
@endsection

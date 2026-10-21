@extends('layouts.app')

@section('title', 'Sửa sản phẩm - GunplaHub')

@section('content')
<div class="card shadow-sm mb-4">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Sửa sản phẩm <code class="text-primary">{{ $product->sku }}</code></h4>
            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>Quay lại danh sách
            </a>
        </div>

        <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('admin.products._form')

            <div class="mt-4 pt-3 border-top">
                <button type="submit" class="btn btn-dark">
                    <i class="bi bi-check-lg me-1"></i>Lưu thay đổi
                </button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary ms-2">Hủy</a>
            </div>
        </form>
    </div>
</div>

{{-- Bảng lịch sử thay đổi giá --}}
<div class="card shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 text-dark">
            <i class="bi bi-clock-history text-primary me-2"></i>Lịch sử thay đổi giá (Price History)
        </h5>
        <span class="badge bg-secondary-subtle text-secondary border">
            {{ $product->priceHistories->count() }} lượt ghi nhận
        </span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Thời gian</th>
                    <th>Loại giá</th>
                    <th class="text-end">Giá nhập (VNĐ)</th>
                    <th class="text-end">Giá bán (VNĐ)</th>
                    <th class="text-end">Chênh lệch (Lãi gộp)</th>
                    <th>Người thực hiện</th>
                    <th>Ghi chú / Lý do điều chỉnh</th>
                </tr>
            </thead>
            <tbody>
                @forelse($product->priceHistories as $history)
                    @php
                        $margin = $history->margin();
                        $marginPct = $history->marginPercent();
                    @endphp
                    <tr>
                        <td class="text-nowrap small">
                            <i class="bi bi-calendar3 me-1 text-muted"></i>
                            {{ $history->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td>
                            <span class="badge text-bg-light border fw-semibold">
                                {{ $history->price_type }}
                            </span>
                        </td>
                        <td class="text-end">
                            @if($history->old_cost_price !== null && (float)$history->old_cost_price !== (float)$history->cost_price)
                                <div class="text-muted small text-decoration-line-through">
                                    {{ number_format($history->old_cost_price) }} đ
                                </div>
                            @endif
                            <div class="fw-semibold text-secondary">
                                {{ number_format($history->cost_price) }} đ
                            </div>
                        </td>
                        <td class="text-end">
                            @if($history->old_price !== null && (float)$history->old_price !== (float)$history->price)
                                <div class="text-muted small text-decoration-line-through">
                                    {{ number_format($history->old_price) }} đ
                                </div>
                            @endif
                            <div class="fw-bold text-dark">
                                {{ number_format($history->price) }} đ
                            </div>
                        </td>
                        <td class="text-end">
                            <span class="badge {{ $margin >= 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} border px-2 py-1">
                                {{ $margin >= 0 ? '+' : '' }}{{ number_format($margin) }} đ
                                <small>({{ $margin >= 0 ? '+' : '' }}{{ $marginPct }}%)</small>
                            </span>
                        </td>
                        <td class="small">
                            @if($history->user)
                                <i class="bi bi-person-check text-success me-1"></i>{{ $history->user->full_name }}
                            @else
                                <span class="text-muted">Hệ thống</span>
                            @endif
                        </td>
                        <td class="text-muted small">
                            {{ $history->note ?: '—' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="bi bi-info-circle me-1"></i>Chưa có lịch sử thay đổi giá nào được ghi nhận cho sản phẩm này.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

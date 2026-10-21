@extends('layouts.app')

@section('title', 'Quản lý mã giảm giá - Admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0"><i class="bi bi-ticket-perforated me-2"></i>Quản lý mã giảm giá</h3>
    <a href="{{ route('admin.vouchers.create') }}" class="btn btn-dark">
        <i class="bi bi-plus-lg me-1"></i>Thêm mã giảm giá
    </a>
</div>

{{-- Bộ lọc & Tìm kiếm --}}
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.vouchers.index') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="q" class="form-control" placeholder="Tìm theo mã giảm giá..."
                           value="{{ request('q') }}">
                </div>
            </div>
            <div class="col-md-4">
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="active" @selected(request('status') === 'active')>Đang áp dụng</option>
                    <option value="expired" @selected(request('status') === 'expired')>Hết hạn</option>
                    <option value="depleted" @selected(request('status') === 'depleted')>Hết lượt</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Tạm ngừng</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-secondary flex-grow-1">Lọc</button>
                @if(request()->hasAny(['q', 'status']))
                    <a href="{{ route('admin.vouchers.index') }}" class="btn btn-outline-secondary">Xóa lọc</a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Bảng danh sách mã giảm giá --}}
<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Mã giảm giá</th>
                    <th class="text-end">Số tiền giảm</th>
                    <th class="text-end">Đơn tối thiểu</th>
                    <th class="text-center">Lượt sử dụng</th>
                    <th>Ngày hết hạn</th>
                    <th class="text-center">Trạng thái</th>
                    <th class="text-end">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vouchers as $voucher)
                    <tr>
                        <td>
                            <strong class="font-monospace text-primary fs-6">{{ $voucher->code }}</strong>
                        </td>
                        <td class="text-end text-success fw-bold">
                            -{{ number_format($voucher->discount_amount) }} đ
                        </td>
                        <td class="text-end">
                            {{ number_format($voucher->min_order_value) }} đ
                        </td>
                        <td class="text-center">
                            <span class="fw-semibold">{{ $voucher->used_count }}</span>
                            /
                            <span class="text-muted">{{ $voucher->usage_limit ?? '∞' }}</span>
                        </td>
                        <td>
                            <span class="{{ $voucher->isExpired() ? 'text-danger fw-semibold' : '' }}">
                                {{ $voucher->expiry_date->format('d/m/Y H:i') }}
                            </span>
                            @if($voucher->isExpired())
                                <span class="badge text-bg-danger ms-1">Đã qua</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $voucher->statusBadgeClass() }}">
                                {{ $voucher->status() }}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.vouchers.edit', $voucher) }}" class="btn btn-outline-primary" title="Sửa">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.vouchers.destroy', $voucher) }}"
                                      onsubmit="return confirm('Bạn có chắc muốn xóa hoặc tạm ngừng mã giảm giá {{ $voucher->code }}?');"
                                      class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Xóa/Tạm ngừng">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            Chưa có mã giảm giá nào phù hợp.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($vouchers->hasPages())
        <div class="card-footer bg-white py-3">
            {{ $vouchers->links() }}
        </div>
    @endif
</div>
@endsection

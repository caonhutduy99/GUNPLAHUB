@extends('layouts.app')

@section('title', 'Khách hàng - GunplaHub')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Quản lý khách hàng</h3>
    @if($blacklistCount > 0)
        <a href="{{ route('admin.customers.index', ['status' => 'blacklist']) }}" class="badge text-bg-danger text-decoration-none fs-6">
            {{ $blacklistCount }} khách trong Blacklist
        </a>
    @endif
</div>

<form method="GET" action="{{ route('admin.customers.index') }}" class="row g-2 mb-3">
    <div class="col-md-5">
        <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Tìm theo tên, email hoặc số điện thoại">
    </div>
    <div class="col-md-3">
        <select name="status" class="form-select">
            <option value="">Tất cả</option>
            <option value="normal" @selected(request('status') === 'normal')>Đang hoạt động</option>
            <option value="blacklist" @selected(request('status') === 'blacklist')>Trong Blacklist</option>
        </select>
    </div>
    <div class="col-md-4 d-flex gap-1">
        <button type="submit" class="btn btn-secondary flex-fill">Lọc</button>
        <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary">Xóa lọc</a>
    </div>
</form>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Khách hàng</th>
                    <th>Số điện thoại</th>
                    <th class="text-center">Số đơn</th>
                    <th class="text-end">Đã chi tiêu</th>
                    <th>Ngày đăng ký</th>
                    <th>Trạng thái</th>
                    <th style="width: 90px;"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $customer->full_name }}</div>
                            <div class="small text-muted">{{ $customer->email }}</div>
                        </td>
                        <td>{{ $customer->phone }}</td>
                        <td class="text-center">{{ $customer->orders_count }}</td>
                        <td class="text-end">{{ \App\Support\Money::format($customer->total_spent ?? 0) }}</td>
                        <td class="small">{{ $customer->created_at->format('d/m/Y') }}</td>
                        <td>
                            @if($customer->is_blacklisted)
                                <span class="badge text-bg-danger">Blacklist</span>
                            @else
                                <span class="badge text-bg-success">Hoạt động</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-sm btn-outline-primary">Xem</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">Không có khách hàng nào.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $customers->links('pagination::bootstrap-5') }}
</div>
@endsection

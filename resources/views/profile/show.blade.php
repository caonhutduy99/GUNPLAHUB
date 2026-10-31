@extends('layouts.app')

@section('title', 'Hồ sơ cá nhân - ' . config('shop.name'))

@section('content')
<div class="row g-4">
    {{-- Cột trái: Tóm tắt thông tin tài khoản --}}
    <div class="col-lg-4">
        <div class="card shadow-sm mb-4">
            <div class="card-body text-center p-4">
                <div class="rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center mb-3"
                     style="width: 80px; height: 80px; font-size: 2.5rem;">
                    <x-icon name="user-round" size="2.5rem" />
                </div>
                <h5 class="mb-1 fw-bold">{{ $user->full_name }}</h5>
                <p class="text-muted small mb-2">{{ $user->email }}</p>
                <div>
                    @if($user->isAdmin())
                        <span class="badge text-bg-danger"><x-icon name="lock" class="me-1" />Quản trị viên</span>
                    @else
                        <span class="badge text-bg-success"><x-icon name="badge-check" class="me-1" />Khách hàng thành viên</span>
                    @endif

                    @if($user->isAdmin())
                        <div class="mt-3">
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-dark btn-sm w-100">
                                <x-icon name="layout-grid" class="me-1" />Bảng điều khiển
                            </a>
                        </div>
                    @endif

                    @if($user->is_blacklisted)
                        <span class="badge text-bg-dark ms-1">Hạn chế đặt hàng</span>
                    @endif
                </div>

                <hr class="my-3">

                <div class="row text-center g-2">
                    <div class="col-6">
                        <div class="p-2 border rounded bg-body-tertiary">
                            <div class="text-muted small">Đơn hàng</div>
                            <div class="fs-5 fw-bold text-dark">{{ $ordersCount }}</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 border rounded bg-body-tertiary">
                            <div class="text-muted small">Đã chi tiêu</div>
                            <div class="fs-6 fw-bold text-success">{{ number_format($totalSpent) }} đ</div>
                        </div>
                    </div>
                </div>

                @unless($user->isAdmin())
                    <div class="mt-3">
                        <a href="{{ route('orders.index') }}" class="btn btn-outline-dark btn-sm w-100">
                            <x-icon name="receipt" class="me-1" />Xem tất cả đơn hàng
                        </a>
                        <a href="{{ route('wishlist.index') }}" class="btn btn-outline-danger btn-sm w-100 mt-2">
                            <x-icon name="heart" class="me-1" />Danh sách yêu thích
                        </a>
                    </div>
                @endunless
            </div>
            <div class="card-footer bg-white small text-muted">
                <x-icon name="calendar" class="me-1" />Tham gia từ: {{ $user->created_at->format('d/m/Y') }}
            </div>
        </div>

        {{-- Đơn hàng gần nhất --}}
        @unless($user->isAdmin())
            @if($recentOrders->isNotEmpty())
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold"><x-icon name="refresh-cw" class="me-1" />Đơn hàng gần đây</h6>
                    </div>
                    <ul class="list-group list-group-flush small">
                        @foreach($recentOrders as $order)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <a href="{{ route('orders.show', $order) }}" class="fw-semibold text-decoration-none">
                                        #{{ $order->id }}
                                    </a>
                                    <div class="text-muted" style="font-size: 0.75rem;">
                                        {{ $order->created_at->format('d/m/Y H:i') }}
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-light text-dark border">{{ $order->status }}</span>
                                    <div class="fw-bold text-success" style="font-size: 0.8rem;">
                                        {{ number_format($order->total_amount) }} đ
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        @endunless
    </div>

    {{-- Cột phải: Form cập nhật thông tin & Đổi mật khẩu --}}
    <div class="col-lg-8">
        {{-- Form cập nhật hồ sơ cá nhân --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><x-icon name="user-cog" class="text-primary me-2" />Thông tin tài khoản</h5>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('profile.update') }}" class="needs-constraint-validation" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="full_name" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                        <input type="text" id="full_name" name="full_name"
                               value="{{ old('full_name', $user->full_name) }}"
                               class="form-control @error('full_name') is-invalid @enderror" required>
                        @error('full_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="email" class="form-label">Địa chỉ Email</label>
                            <input type="email" id="email" value="{{ $user->email }}" class="form-control bg-light" readonly>
                            <div class="form-text">Email dùng để đăng nhập, không thể thay đổi.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="phone" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="text" id="phone" name="phone"
                                   value="{{ old('phone', $user->phone) }}"
                                   class="form-control @error('phone') is-invalid @enderror"
                                   placeholder="Ví dụ: 0912345678" required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Địa chỉ giao hàng mặc định</label>
                        <textarea id="address" name="address" rows="3"
                                  class="form-control @error('address') is-invalid @enderror"
                                  placeholder="Nhập số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành phố...">{{ old('address', $user->address) }}</textarea>
                        <div class="form-text">Địa chỉ này sẽ được tự động điền khi bạn tiến hành đặt hàng.</div>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="btn btn-dark">
                            <x-icon name="save" class="me-1" />Lưu thông tin cá nhân
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Form đổi mật khẩu --}}
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><x-icon name="lock" class="text-danger me-2" />Đổi mật khẩu</h5>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('profile.password') }}" class="needs-constraint-validation" data-validation-mode="password-change" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="current_password" class="form-label">Mật khẩu hiện tại <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" id="current_password" name="current_password"
                                   class="form-control @error('current_password') is-invalid @enderror" required>
                            <button class="btn btn-outline-secondary btn-toggle-password" type="button" aria-label="Hiện mật khẩu">
                                <x-icon name="eye" />
                            </button>
                        </div>
                        @error('current_password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="password" class="form-label">Mật khẩu mới <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" id="password" name="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       placeholder="Tối thiểu 8 ký tự" required>
                                <button class="btn btn-outline-secondary btn-toggle-password" type="button" aria-label="Hiện mật khẩu">
                                    <x-icon name="eye" />
                                </button>
                            </div>
                            <div class="password-checklist small mt-2" data-password-checklist></div>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label">Xác nhận mật khẩu mới <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                       class="form-control" placeholder="Nhập lại mật khẩu mới" required>
                                <button class="btn btn-outline-secondary btn-toggle-password" type="button" aria-label="Hiện mật khẩu">
                                    <x-icon name="eye" />
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="btn btn-outline-danger">
                            <x-icon name="key-round" class="me-1" />Cập nhật mật khẩu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

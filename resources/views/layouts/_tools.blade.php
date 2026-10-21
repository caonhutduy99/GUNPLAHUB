{{-- Ô tìm kiếm, nút sáng/tối, đăng nhập/đăng ký, tài khoản, giỏ hàng. Dùng ở thanh menu và ở banner trang chủ. --}}
@php
    $toolsIsAdmin = auth()->check() && auth()->user()->isAdmin();
    $toolsCartCount = (auth()->check() && ! $toolsIsAdmin)
        ? (int) auth()->user()->cartItems()->sum('quantity')
        : 0;
@endphp
<div class="d-flex flex-wrap align-items-center gap-3 text-white">
    <form class="d-flex" method="GET" action="{{ route('products.index') }}">
        <input class="form-control form-control-sm me-1" type="search" name="q"
               value="{{ request()->routeIs('products.index') ? request('q') : '' }}"
               placeholder="Tìm mô hình...">
        <button class="btn btn-outline-light btn-sm" type="submit">Tìm</button>
    </form>

    <button type="button" id="themeToggle" class="btn btn-outline-light btn-sm"
            title="Đổi giao diện sáng/tối"></button>

    @guest
        <a href="{{ route('login') }}" class="link-light text-decoration-none">Đăng nhập</a>
        <a href="{{ route('register') }}" class="link-light text-decoration-none">Đăng ký</a>
    @else
        {{-- Thông tin khách hàng, nút đăng xuất nằm bên dưới --}}
        <div>
            <div class="small lh-sm">Xin chào, <a href="{{ route('profile.show') }}" class="link-light fw-bold text-decoration-none" title="Xem hồ sơ cá nhân">{{ auth()->user()->full_name }}</a></div>
            <div class="d-flex align-items-center gap-1 mt-1">
                <a href="{{ route('profile.show') }}" class="btn btn-outline-light btn-sm py-0 px-2" title="Hồ sơ cá nhân">Hồ sơ</a>
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm py-0 px-2">Đăng xuất</button>
                </form>
            </div>
        </div>

        @if($toolsIsAdmin)
            @if($showAdminLink ?? false)
                <a href="{{ route('admin.dashboard') }}" class="btn btn-warning btn-sm">
                    <i class="bi bi-gear-fill me-1"></i>Quản trị
                </a>
            @endif
        @else
            <a href="{{ route('orders.index') }}" class="link-light text-decoration-none">
                <i class="bi bi-receipt"></i> Đơn hàng
            </a>

            {{-- Giỏ hàng nằm bên phải thông tin khách hàng --}}
            <a href="{{ route('cart.index') }}" class="link-light text-decoration-none">
                <i class="bi bi-cart3 fs-5"></i> Giỏ hàng
                @if($toolsCartCount > 0)
                    <span class="badge rounded-pill text-bg-danger">{{ $toolsCartCount }}</span>
                @endif
            </a>
        @endif
    @endguest
</div>

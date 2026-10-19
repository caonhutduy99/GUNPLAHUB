@php
    $heroUser = auth()->user();
    $heroIsAdmin = $heroUser && $heroUser->isAdmin();
    $heroCartCount = ($heroUser && ! $heroIsAdmin) ? (int) $heroUser->cartItems()->sum('quantity') : 0;
@endphp

{{-- Tìm kiếm, đổi giao diện, tài khoản: nằm cùng hàng với chữ GunplaHub trong banner --}}
<div class="d-flex flex-wrap align-items-center gap-2">
    <form class="d-flex" method="GET" action="{{ route('products.index') }}">
        <input class="form-control form-control-sm me-1" type="search" name="q" placeholder="Tìm mô hình..." style="min-width: 170px;">
        <button class="btn btn-outline-light btn-sm" type="submit">Tìm</button>
    </form>

    <button type="button" id="themeToggle" class="btn btn-outline-light btn-sm" title="Đổi giao diện sáng/tối"></button>

    @guest
        <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm">Đăng nhập</a>
        <a href="{{ route('register') }}" class="btn btn-light btn-sm">Đăng ký</a>
    @else
        {{-- Thông tin khách hàng, nút đăng xuất nằm bên dưới --}}
        <div class="ms-lg-2">
            <div class="small lh-sm">Xin chào, <strong>{{ $heroUser->full_name }}</strong></div>
            <form method="POST" action="{{ route('logout') }}" class="mt-1">
                @csrf
                <button type="submit" class="btn btn-outline-light btn-sm py-0 px-2">Đăng xuất</button>
            </form>
        </div>

        {{-- Giỏ hàng nằm bên phải thông tin khách hàng; admin thì có nút Quản trị --}}
        @if($heroIsAdmin)
            <a href="{{ route('admin.dashboard') }}" class="btn btn-light btn-sm">Quản trị</a>
        @else
            <a href="{{ route('cart.index') }}" class="btn btn-outline-light btn-sm">
                <i class="bi bi-cart3"></i> Giỏ hàng
                @if($heroCartCount > 0)
                    <span class="badge rounded-pill text-bg-danger">{{ $heroCartCount }}</span>
                @endif
            </a>
        @endif
    @endguest
</div>

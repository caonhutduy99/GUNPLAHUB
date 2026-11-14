{{-- Thanh công cụ đầu trang: tìm kiếm, giao diện sáng/tối, đơn hàng, tài khoản, giỏ hàng --}}
@php
    $toolsUser = auth()->user();
    $toolsIsAdmin = $toolsUser && $toolsUser->isAdmin();
    $toolsCartCount = ($toolsUser && ! $toolsIsAdmin)
        ? (int) $toolsUser->cartItems()->sum('quantity')
        : 0;
@endphp
<div class="gh-tools d-flex align-items-center flex-wrap ms-lg-auto">
    @unless($toolsIsAdmin)
        <form class="gh-search" method="GET" action="{{ route('products.index') }}" role="search">
            <x-icon name="search" size="1rem" />
            <input class="form-control"
                   type="search" name="q"
                   value="{{ request()->routeIs('products.index') ? request('q') : '' }}"
                   placeholder="Tìm Gunpla, phụ kiện..."
                   aria-label="Tìm kiếm sản phẩm">
        </form>
    @endunless

    {{-- Đổi giao diện sáng/tối: script ở layout chèn icon vào nút này --}}
    <button type="button" id="themeToggle" class="gh-iconbtn theme-toggle"
            title="Đổi giao diện sáng/tối" aria-label="Đổi giao diện sáng/tối"></button>

    @unless($toolsIsAdmin)
        <a href="{{ route('orders.index') }}" class="gh-iconbtn" title="Đơn hàng của tôi" aria-label="Đơn hàng của tôi">
            <x-icon name="package-open" size="1.1rem" />
        </a>
    @endunless

    @guest
        <a href="{{ route('login') }}" class="btn btn-outline-secondary gh-cart">Đăng nhập</a>
        <a href="{{ route('register') }}" class="btn btn-outline-primary gh-cart">Đăng ký</a>
    @else
        <div class="dropdown">
            <button class="gh-account" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="gh-avatar">{{ mb_strtoupper(mb_substr($toolsUser->full_name, 0, 1)) }}</span>
                <span class="gh-account-name">{{ $toolsUser->full_name }}</span>
                <x-icon name="chevron-down" size="0.8rem" class="text-muted" />
            </button>
            <ul class="dropdown-menu dropdown-menu-end" style="z-index: 1060; min-width: 200px;">
                @if($toolsIsAdmin)
                    <li><span class="dropdown-header">Quản trị viên</span></li>
                @endif
                <li><a class="dropdown-item" href="{{ route('profile.show') }}"><x-icon name="user" class="me-2" />Tài khoản</a></li>
                @unless($toolsIsAdmin)
                    <li><a class="dropdown-item" href="{{ route('orders.index') }}"><x-icon name="package-open" class="me-2" />Đơn hàng của tôi</a></li>
                @endunless
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <x-icon name="log-out" class="me-2" />Đăng xuất
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    @endguest

    @unless($toolsIsAdmin)
        <a href="{{ route('cart.index') }}" class="btn btn-primary gh-cart text-decoration-none" title="Giỏ hàng">
            <x-icon name="shopping-cart" size="1.1rem" />
            <span>Giỏ hàng</span>
            @if($toolsCartCount > 0)
                <span class="gh-cart-count">{{ $toolsCartCount }}</span>
            @endif
        </a>
    @endunless
</div>

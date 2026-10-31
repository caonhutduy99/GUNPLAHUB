{{-- Thanh công cụ Header: Ô tìm kiếm to, Theme & Đơn hàng, Giỏ hàng, Tài khoản 2 khối --}}
@php
    $toolsUser = auth()->user();
    $toolsIsAdmin = $toolsUser && $toolsUser->isAdmin();
    $toolsCartCount = ($toolsUser && ! $toolsIsAdmin)
        ? (int) $toolsUser->cartItems()->sum('quantity')
        : 0;
@endphp
<div class="gh-tools-shell d-flex align-items-center gap-1 ms-auto">
    {{-- 1. Ô tìm kiếm --}}
    @unless($toolsIsAdmin)
        <form class="gh-search-form position-relative d-flex align-items-center" method="GET" action="{{ route('products.index') }}">
            <input class="gh-search-input form-control rounded-pill pe-5"
                   type="search" name="q"
                   value="{{ request()->routeIs('products.index') ? request('q') : '' }}"
                   placeholder="Tìm kiếm Gunpla, phụ kiện..."
                   style="width: 270px; min-height: 46px; font-size: 0.85rem; padding-left: 0.9rem;">
            <button class="gh-search-button btn btn-link btn-sm position-absolute end-0 top-50 translate-middle-y me-2 p-0 border-0"
                    type="submit" aria-label="Tìm kiếm">
                <x-icon name="search" size="1rem" />
            </button>
        </form>
    @endunless

    {{-- 2. Biểu tượng Theme (trên) & Đơn hàng (dưới) --}}
    <div class="d-flex flex-column gap-1 align-items-center justify-content-center" style="height: 50px;">
        {{-- Biểu tượng Theme tròn nhỏ --}}
        <button type="button" id="themeToggle"
                class="gh-tools-circle btn btn-sm rounded-circle p-0 d-inline-flex align-items-center justify-content-center"
                title="Đổi giao diện sáng/tối"
                style="width:24px;height:24px;font-size:0.72rem;"
                aria-label="Đổi giao diện sáng/tối">
        </button>

        {{-- Biểu tượng Đơn hàng tròn nhỏ --}}
        @auth
            @unless($toolsIsAdmin)
                <a href="{{ route('orders.index') }}"
                   class="gh-tools-circle btn btn-sm rounded-circle p-0 d-inline-flex align-items-center justify-content-center"
                   title="Đơn hàng của tôi"
                   style="width:24px;height:24px;font-size:0.72rem;"
                   aria-label="Đơn hàng của tôi">
                    <x-icon name="package-open" />
                </a>
            @else
                <a href="{{ route('admin.orders.index') }}"
                   class="gh-tools-circle btn btn-sm rounded-circle p-0 d-inline-flex align-items-center justify-content-center"
                   title="Quản lý đơn hàng"
                   style="width: 24px; height: 24px; font-size: 0.72rem;"
                   aria-label="Quản lý đơn hàng">
                    <x-icon name="receipt" />
                </a>
            @endunless
        @else
            <a href="{{ route('orders.index') }}"
               class="gh-tools-circle btn btn-sm rounded-circle p-0 d-inline-flex align-items-center justify-content-center"
               title="Đơn hàng"
               style="width: 24px; height: 24px; font-size: 0.72rem;"
               aria-label="Đơn hàng">
                <x-icon name="package-open" />
            </a>
        @endauth
    </div>


    {{-- 3. Tài khoản --}}
    @guest
        <div class="gh-account-tools d-flex flex-column gap-1 justify-content-center flex-shrink-0" style="height: 50px;">
            <a href="{{ route('login') }}" class="gh-login-button btn btn-sm rounded-pill px-2 py-1 text-center" style="font-size: 0.8rem; line-height: 1.1;">
                <x-icon name="user-round" class="me-1" />Đăng nhập
            </a>
            <a href="{{ route('register') }}" class="btn btn-danger btn-sm rounded-pill px-2 py-1 text-center" style="font-size: 0.8rem; line-height: 1.1;">
                Đăng ký
            </a>
        </div>
    @else
        <div class="gh-account-tools dropdown flex-shrink-0">
            <button class="gh-account-button btn rounded-3 px-3 d-inline-flex align-items-center justify-content-center gap-2 shadow-sm text-start"
                    type="button" data-bs-toggle="dropdown" aria-expanded="false"
                    style="height: 50px; width: 150px;">
                <div class="rounded-circle bg-danger-subtle text-danger d-flex align-items-center justify-content-center flex-shrink-0" style="width: 29px; height: 29px;">
                    <x-icon name="{{ $toolsIsAdmin ? 'lock' : 'user-round' }}" size="1.25rem" />
                </div>
                <div class="lh-sm me-1">
                    <div class="gh-account-label text-uppercase fw-semibold" style="font-size: 0.6rem; letter-spacing: 0.03em;">Tài khoản</div>
                    <div class="gh-account-name fw-bold text-truncate" style="max-width: 100px; font-size: 0.78rem;">{{ $toolsUser->full_name }}</div>
                </div>
                <x-icon name="chevron-down" class="gh-account-chevron small ms-auto" />
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="border-radius: 12px; font-size: 0.9rem; z-index: 1060;">
                @if($toolsIsAdmin)
                    <li><span class="dropdown-header text-uppercase small fw-bold">Quản trị viên</span></li>
                    <li><a class="dropdown-item py-2" href="{{ route('profile.show') }}"><x-icon name="user-round" class="me-2" />Tài khoản</a></li>
                @else
                    <li><a class="dropdown-item py-2" href="{{ route('profile.show') }}"><x-icon name="user-round" class="me-2" />Tài khoản</a></li>
                    <li><a class="dropdown-item py-2" href="{{ route('orders.index') }}"><x-icon name="package-open" class="me-2" />Đơn hàng của tôi</a></li>
                @endif
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item py-2 text-danger">
                            <x-icon name="log-out" class="me-2" />Đăng xuất
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    @endguest

        {{-- 4. Giỏ hàng --}}
    @unless($toolsIsAdmin)
        <a href="{{ route('cart.index') }}"
           class="btn btn-danger rounded-3 px-3 d-inline-flex align-items-center justify-content-center gap-2 text-decoration-none shadow-sm flex-shrink-0"
           style="height: 50px; font-weight: 600;"
           title="Giỏ hàng">
            <x-icon name="shopping-cart" size="1.5rem" />
            <div class="text-start lh-sm">
                <div style="font-size: 0.78rem; font-weight: 700;">Giỏ hàng</div>
                @if($toolsUser)
                    @if($toolsCartCount > 0)
                        <span class="badge bg-white text-danger fw-bold rounded-pill" style="font-size: 0.72rem; padding: 2px 7px;">
                            {{ $toolsCartCount }}
                        </span>
                    @endif
                @endif
            </div>
        </a>
    @endunless
</div>

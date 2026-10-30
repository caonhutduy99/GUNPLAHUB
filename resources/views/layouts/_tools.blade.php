{{-- Thanh công cụ Header: Ô tìm kiếm to, Theme & Đơn hàng, Giỏ hàng, Tài khoản 2 khối --}}
@php
    $toolsUser = auth()->user();
    $toolsIsAdmin = $toolsUser && $toolsUser->isAdmin();
    $toolsCartCount = ($toolsUser && ! $toolsIsAdmin)
        ? (int) $toolsUser->cartItems()->sum('quantity')
        : 0;
@endphp
<div class="d-flex align-items-center gap-2 text-white ms-auto">
    {{-- 1. Ô tìm kiếm --}}
    <form class="position-relative d-flex align-items-center" method="GET" action="{{ route('products.index') }}">
        <input class="form-control rounded-pill bg-dark-subtle border-secondary-subtle text-white pe-5" 
               type="search" name="q"
               value="{{ request()->routeIs('products.index') ? request('q') : '' }}"
               placeholder="Tìm kiếm Gunpla, phụ kiện..."
               style="width: 300px; min-height: 50px; font-size: 0.92rem; padding-left: 1.15rem;">
        <button class="btn btn-link btn-sm text-white-50 position-absolute end-0 top-50 translate-middle-y me-2 p-0 border-0" 
                type="submit" aria-label="Tìm kiếm">
            <i class="bi bi-search" style="font-size: 1rem;"></i>
        </button>
    </form>

    {{-- 2. Biểu tượng Theme (trên) & Đơn hàng (dưới) --}}
    <div class="d-flex flex-column gap-1 align-items-center justify-content-center" style="height: 58px;">
        {{-- Biểu tượng Theme tròn nhỏ --}}
        <button type="button" id="themeToggle" 
                class="btn btn-outline-light btn-sm rounded-circle p-0 d-inline-flex align-items-center justify-content-center"
                title="Đổi giao diện sáng/tối" 
                style="width: 27px; height: 27px; font-size: 0.8rem;"
                aria-label="Đổi giao diện sáng/tối">
        </button>

        {{-- Biểu tượng Đơn hàng tròn nhỏ --}}
        @auth
            @unless($toolsIsAdmin)
                <a href="{{ route('orders.index') }}" 
                   class="btn btn-outline-light btn-sm rounded-circle p-0 d-inline-flex align-items-center justify-content-center text-white" 
                   title="Đơn hàng của tôi" 
                   style="width: 27px; height: 27px; font-size: 0.8rem;" 
                   aria-label="Đơn hàng của tôi">
                    <i class="bi bi-box-seam"></i>
                </a>
            @else
                <a href="{{ route('admin.orders.index') }}" 
                   class="btn btn-outline-light btn-sm rounded-circle p-0 d-inline-flex align-items-center justify-content-center text-white" 
                   title="Quản lý đơn hàng" 
                   style="width: 27px; height: 27px; font-size: 0.8rem;" 
                   aria-label="Quản lý đơn hàng">
                    <i class="bi bi-receipt"></i>
                </a>
            @endunless
        @else
            <a href="{{ route('orders.index') }}" 
               class="btn btn-outline-light btn-sm rounded-circle p-0 d-inline-flex align-items-center justify-content-center text-white" 
               title="Đơn hàng" 
               style="width: 27px; height: 27px; font-size: 0.8rem;" 
               aria-label="Đơn hàng">
                <i class="bi bi-box-seam"></i>
            </a>
        @endauth
    </div>


    {{-- 3. Tài khoản --}}
    @guest
        <div class="d-flex flex-column gap-1 justify-content-center flex-shrink-0" style="height: 58px;">
            <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm rounded-pill px-3 py-1 text-center" style="font-size: 0.9rem; line-height: 1.2;">
                <i class="bi bi-person me-1"></i>Đăng nhập
            </a>
            <a href="{{ route('register') }}" class="btn btn-danger btn-sm rounded-pill px-3 py-1 text-center" style="font-size: 0.9rem; line-height: 1.2;">
                Đăng ký
            </a>
        </div>
    @else
        <div class="dropdown flex-shrink-0">
            <button class="btn btn-outline-light rounded-3 px-3 d-inline-flex align-items-center justify-content-center gap-2 shadow-sm text-start" 
                    type="button" data-bs-toggle="dropdown" aria-expanded="false" 
                    style="height: 58px; min-width: 140px;">
                <div class="rounded-circle bg-danger-subtle text-danger d-flex align-items-center justify-content-center flex-shrink-0" style="width: 34px; height: 34px;">
                    <i class="bi {{ $toolsIsAdmin ? 'bi-shield-lock-fill' : 'bi-person-fill' }} fs-5"></i>
                </div>
                <div class="lh-sm me-1">
                    <div class="text-white-50 text-uppercase fw-semibold" style="font-size: 0.68rem; letter-spacing: 0.04em;">Tài khoản</div>
                    <div class="fw-bold text-white text-truncate" style="max-width: 120px; font-size: 0.88rem;">{{ $toolsUser->full_name }}</div>
                </div>
                <i class="bi bi-chevron-down small text-white-50 ms-auto"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="border-radius: 12px; font-size: 0.9rem; z-index: 1060;">
                @if($toolsIsAdmin)
                    <li><span class="dropdown-header text-uppercase small fw-bold">Quản trị viên</span></li>
                    <li><a class="dropdown-item py-2" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Bảng điều khiển</a></li>
                    <li><a class="dropdown-item py-2" href="{{ route('profile.show') }}"><i class="bi bi-person me-2"></i>Tài khoản</a></li>
                @else
                    <li><a class="dropdown-item py-2" href="{{ route('profile.show') }}"><i class="bi bi-person me-2"></i>Tài khoản</a></li>
                    <li><a class="dropdown-item py-2" href="{{ route('orders.index') }}"><i class="bi bi-box-seam me-2"></i>Đơn hàng của tôi</a></li>
                @endif
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item py-2 text-danger">
                            <i class="bi bi-box-arrow-right me-2"></i>Đăng xuất
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
           style="height: 58px; font-weight: 600;"
           title="Giỏ hàng">
            <i class="bi bi-cart3 fs-4"></i>
            <div class="text-start lh-sm">
                <div style="font-size: 0.88rem; font-weight: 700;">Giỏ hàng</div>
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

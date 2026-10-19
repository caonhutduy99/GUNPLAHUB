@php
    $shopPhone = config('shop.phone');
    $phoneDigits = preg_replace('/\D/', '', $shopPhone);
    $cartCount = (auth()->check() && ! auth()->user()->isAdmin())
        ? (int) auth()->user()->cartItems()->sum('quantity')
        : 0;
@endphp
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'GunplaHub')</title>

    {{-- Đặt theme ngay từ đầu để trang không bị chớp sáng/tối --}}
    <script>
        (function () {
            var theme = null;
            try { theme = localStorage.getItem('theme'); } catch (e) {}
            if (!theme) {
                theme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }
            document.documentElement.setAttribute('data-bs-theme', theme);
        })();
    </script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        /* Chỉnh các thành phần dùng màu cố định để đọc được ở chế độ tối */
        [data-bs-theme="dark"] .btn-dark {
            --bs-btn-color: #212529; --bs-btn-bg: #dee2e6; --bs-btn-border-color: #dee2e6;
            --bs-btn-hover-color: #212529; --bs-btn-hover-bg: #fff; --bs-btn-hover-border-color: #fff;
            --bs-btn-active-color: #212529; --bs-btn-active-bg: #fff; --bs-btn-active-border-color: #fff;
            --bs-btn-disabled-color: #212529; --bs-btn-disabled-bg: #dee2e6; --bs-btn-disabled-border-color: #dee2e6;
        }
        [data-bs-theme="dark"] .btn-outline-dark {
            --bs-btn-color: #dee2e6; --bs-btn-border-color: #dee2e6;
            --bs-btn-hover-color: #212529; --bs-btn-hover-bg: #dee2e6; --bs-btn-hover-border-color: #dee2e6;
            --bs-btn-active-color: #212529; --bs-btn-active-bg: #dee2e6; --bs-btn-active-border-color: #dee2e6;
        }
        [data-bs-theme="dark"] .text-bg-dark { background-color: #495057 !important; color: #fff !important; }
        [data-bs-theme="dark"] .table-light {
            --bs-table-bg: var(--bs-tertiary-bg);
            --bs-table-color: var(--bs-body-color);
            --bs-table-border-color: var(--bs-border-color);
        }

        /* Nút nổi góc phải */
        .float-btn {
            width: 48px; height: 48px; border-radius: 50%; border: 0; padding: 0;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 1.25rem; text-decoration: none;
            box-shadow: 0 2px 8px rgba(0,0,0,.35); cursor: pointer;
        }
        .float-btn:hover { color: #fff; filter: brightness(1.1); }
    </style>
    @stack('styles')
</head>
<body class="bg-body-tertiary">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('home') }}">{{ config('shop.name') }}</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('home') ? 'active fw-semibold' : '' }}" href="{{ route('home') }}">Trang chủ</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('products.*') ? 'active fw-semibold' : '' }}" href="{{ route('products.index') }}">Cửa hàng</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('contact') ? 'active fw-semibold' : '' }}" href="{{ route('contact') }}">Liên hệ</a></li>
                @auth
                    @if(auth()->user()->isAdmin())
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active fw-semibold' : '' }}" href="{{ route('admin.dashboard') }}">Quản trị</a></li>
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active fw-semibold' : '' }}" href="{{ route('admin.categories.index') }}">Danh mục</a></li>
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.products.*') ? 'active fw-semibold' : '' }}" href="{{ route('admin.products.index') }}">Sản phẩm</a></li>
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.brands.*') ? 'active fw-semibold' : '' }}" href="{{ route('admin.brands.index') }}">Hãng</a></li>
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.contacts.*') ? 'active fw-semibold' : '' }}" href="{{ route('admin.contacts.index') }}">Tin nhắn</a></li>
                    @endif
                @endauth
            </ul>

            <form class="d-flex me-3 my-2 my-lg-0" method="GET" action="{{ route('products.index') }}">
                <input class="form-control form-control-sm me-1" type="search" name="q"
                       value="{{ request()->routeIs('products.index') ? request('q') : '' }}"
                       placeholder="Tìm mô hình...">
                <button class="btn btn-outline-light btn-sm" type="submit">Tìm</button>
            </form>

            <button type="button" id="themeToggle" class="btn btn-outline-light btn-sm me-3 my-2 my-lg-0"
                    title="Đổi giao diện sáng/tối"></button>

            <ul class="navbar-nav align-items-lg-center">
                @guest
                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Đăng nhập</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Đăng ký</a></li>
                @else
                    {{-- Thông tin khách hàng, nút đăng xuất nằm bên dưới --}}
                    <li class="nav-item me-lg-4 py-2 py-lg-0">
                        <div class="text-white small lh-sm">Xin chào, <strong>{{ auth()->user()->full_name }}</strong></div>
                        <form method="POST" action="{{ route('logout') }}" class="mt-1">
                            @csrf
                            <button type="submit" class="btn btn-outline-light btn-sm py-0 px-2">Đăng xuất</button>
                        </form>
                    </li>

                    {{-- Giỏ hàng nằm bên phải thông tin khách hàng --}}
                    @unless(auth()->user()->isAdmin())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('cart.*') ? 'active fw-semibold' : '' }}" href="{{ route('cart.index') }}">
                                <i class="bi bi-cart3 fs-5"></i> Giỏ hàng
                                @if($cartCount > 0)
                                    <span class="badge rounded-pill text-bg-danger">{{ $cartCount }}</span>
                                @endif
                            </a>
                        </li>
                    @endunless
                @endguest
            </ul>
        </div>
    </div>
</nav>

<main class="container py-4">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning">{{ session('warning') }}</div>
    @endif

    @yield('content')
</main>

<footer class="text-center text-muted py-4 small">
    <div class="mb-1">
        <i class="bi bi-telephone-fill me-1"></i>Hotline: <a href="tel:{{ $phoneDigits }}" class="text-reset">{{ $shopPhone }}</a>
    </div>
    &copy; {{ date('Y') }} {{ config('shop.name') }} - Đồ án môn học
</footer>

{{-- Nút nổi: về đầu trang, gọi điện, Zalo --}}
<div class="position-fixed d-flex flex-column gap-2" style="right: 16px; bottom: 16px; z-index: 1030;">
    <button type="button" id="backToTop" class="float-btn" style="background: #e8501e; display: none;" title="Về đầu trang">
        <i class="bi bi-chevron-up"></i>
    </button>
    <a href="tel:{{ $phoneDigits }}" class="float-btn" style="background: #dc3545;" title="Gọi {{ $shopPhone }}">
        <i class="bi bi-telephone-fill"></i>
    </a>
    <a href="https://zalo.me/{{ $phoneDigits }}" target="_blank" rel="noopener" class="float-btn"
       style="background: #0068ff; font-size: .8rem; font-weight: 700;" title="Chat Zalo">Zalo</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    (function () {
        // Đổi giao diện sáng/tối
        var btn = document.getElementById('themeToggle');
        var root = document.documentElement;

        function render() {
            var dark = root.getAttribute('data-bs-theme') === 'dark';
            btn.textContent = dark ? '☀️ Sáng' : '🌙 Tối';
        }

        btn.addEventListener('click', function () {
            var next = root.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
            root.setAttribute('data-bs-theme', next);
            try { localStorage.setItem('theme', next); } catch (e) {}
            render();
        });
        render();

        // Nút về đầu trang: chỉ hiện khi đã cuộn xuống
        var top = document.getElementById('backToTop');
        window.addEventListener('scroll', function () {
            top.style.display = window.scrollY > 300 ? 'flex' : 'none';
        });
        top.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    })();
</script>
@stack('scripts')
</body>
</html>

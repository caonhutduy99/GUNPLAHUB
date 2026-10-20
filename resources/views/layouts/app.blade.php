@php
    $shopPhone = config('shop.phone');
    $phoneDigits = preg_replace('/\D/', '', $shopPhone);
    $isAdmin = auth()->check() && auth()->user()->isAdmin();
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

{{-- Trang chủ có bảng "Danh sách sản phẩm" thay cho thanh menu nên ẩn thanh menu ở đó --}}
@sectionMissing('no_navbar')
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
                @unless($isAdmin)
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('contact') ? 'active fw-semibold' : '' }}" href="{{ route('contact') }}">Liên hệ</a></li>
                @endunless
                @if($isAdmin)
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active fw-semibold' : '' }}" href="{{ route('admin.dashboard') }}">Quản trị</a></li>
                    @php($pendingOrders = \App\Models\Order::needsAction()->count())
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active fw-semibold' : '' }}" href="{{ route('admin.orders.index') }}">
                            Đơn hàng
                            @if($pendingOrders > 0)
                                <span class="badge rounded-pill text-bg-danger">{{ $pendingOrders }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active fw-semibold' : '' }}" href="{{ route('admin.categories.index') }}">Danh mục</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.products.*') ? 'active fw-semibold' : '' }}" href="{{ route('admin.products.index') }}">Sản phẩm</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.brands.*') ? 'active fw-semibold' : '' }}" href="{{ route('admin.brands.index') }}">Thương hiệu</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.contacts.*') ? 'active fw-semibold' : '' }}" href="{{ route('admin.contacts.index') }}">Tin nhắn</a></li>
                @endif
            </ul>

            @include('layouts._tools')
        </div>
    </div>
</nav>
@endif

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
    @unless($isAdmin)
    <div class="mb-1">
        <i class="bi bi-telephone-fill me-1"></i>Hotline: <a href="tel:{{ $phoneDigits }}" class="text-reset">{{ $shopPhone }}</a>
    </div>
    @endunless
    &copy; {{ date('Y') }} {{ config('shop.name') }} - Đồ án môn học
</footer>

{{-- Nút nổi: về đầu trang, gọi điện, Zalo --}}
<div class="position-fixed d-flex flex-column gap-2" style="right: 16px; bottom: 16px; z-index: 1030;">
    <button type="button" id="backToTop" class="float-btn" style="background: #e8501e; display: none;" title="Về đầu trang">
        <i class="bi bi-chevron-up"></i>
    </button>
    @unless($isAdmin)
    <a href="tel:{{ $phoneDigits }}" class="float-btn" style="background: #dc3545;" title="Gọi {{ $shopPhone }}">
        <i class="bi bi-telephone-fill"></i>
    </a>
    <a href="https://zalo.me/{{ $phoneDigits }}" target="_blank" rel="noopener" class="float-btn"
       style="background: #0068ff; font-size: .8rem; font-weight: 700;" title="Chat Zalo">Zalo</a>
    @endunless
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    (function () {
        // Đổi giao diện sáng/tối
        var btn = document.getElementById('themeToggle');
        var root = document.documentElement;

        function render() {
            if (!btn) { return; }
            var dark = root.getAttribute('data-bs-theme') === 'dark';
            btn.textContent = dark ? '☀️ Sáng' : '🌙 Tối';
        }

        if (btn) btn.addEventListener('click', function () {
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

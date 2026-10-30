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
    <title>@yield('title', 'GunplaHub - Cửa hàng mô hình Gunpla chính hãng')</title>

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

    {{-- Google Font: Be Vietnam Pro (Đẹp, sắc nét, hỗ trợ tiếng Việt hoàn hảo) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --gh-font: 'Be Vietnam Pro', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            --gh-primary: #10152b;
            --gh-primary-hover: #be14ce;
            --gh-accent: #0051ff;
            --gh-alert: #be14ce;
            --gh-secondary: #743cff;
            --gh-soft: #b7ff00;
            --gh-primary-subtle: #e8faff;
            --gh-primary-subtle-text: #07506b;
            --gh-dark: #10152b;
            --gh-slate: #41506b;
            --gh-muted: #68758c;
            --gh-border: #cbd6e6;
            --gh-card-bg: #ffffff;
            --gh-body-bg: #f4f8fc;
            --bs-primary: #10152b;
            --bs-primary-rgb: 16, 21, 43;
            --bs-danger: #e70b0b;
            --bs-danger-rgb: 201, 0, 131;
            --bs-warning: #9ab800;
            --bs-warning-rgb: 154, 184, 0;
            --bs-body-color: #10152b;
            --bs-body-bg: #f4f8fc;
            --gh-radius-sm: 8px;
            --gh-radius: 14px;
            --gh-radius-lg: 20px;
            --gh-shadow-sm: 0 1px 3px rgba(16, 21, 43, 0.08);
            --gh-shadow: 0 8px 24px rgba(16, 21, 43, 0.08);
            --gh-shadow-hover: 0 16px 34px rgba(0, 200, 255, 0.18);
        }

        [data-bs-theme="dark"] {
            --gh-primary: #009dff;
            --gh-primary-hover: #be14ce;
            --gh-accent: #b7ff00;
            --gh-alert: #be14ce;
            --gh-primary-subtle: rgba(0, 200, 255, 0.16);
            --gh-primary-subtle-text: #7be5ff;
            --gh-dark: #f4f8fc;
            --gh-slate: #a9b8ce;
            --gh-muted: #8d9bb2;
            --gh-border: #263b58;
            --gh-card-bg: #0f172a;
            --gh-body-bg: #070b16;
            --bs-primary: #00c8ff;
            --bs-primary-rgb: 0, 200, 255;
            --bs-danger: #e70b0b;
            --bs-danger-rgb: 255, 43, 214;
            --bs-warning: #b7ff00;
            --bs-warning-rgb: 183, 255, 0;
            --bs-body-color: #f4f8fc;
            --bs-body-bg: #070b16;
            --gh-shadow-sm: 0 1px 3px 0 rgba(0, 0, 0, 0.3);
            --gh-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
            --gh-shadow-hover: 0 16px 34px rgba(0, 200, 255, 0.2);
        }

        body {
            font-family: var(--gh-font);
            background-color: var(--gh-body-bg) !important;
            color: var(--bs-body-color);
            letter-spacing: -0.01em;
            -webkit-font-smoothing: antialiased;
        }

        /* Card Styling */
        .card {
            background-color: var(--gh-card-bg) !important;
            border: 1px solid var(--gh-border) !important;
            border-radius: var(--gh-radius) !important;
            box-shadow: var(--gh-shadow-sm) !important;
            transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.25s ease;
        }
        .card:hover {
            border-color: var(--gh-accent) !important;
            box-shadow: var(--gh-shadow-hover) !important;
        }

        /* Buttons */
        .btn {
            font-family: var(--gh-font);
            font-weight: 500;
            border-radius: var(--gh-radius-sm);
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .btn-danger, .btn-primary {
            background-color: var(--gh-primary) !important;
            border-color: var(--gh-primary) !important;
            color: #ffffff !important;
        }
        .btn-danger:hover, .btn-primary:hover {
            background-color: var(--gh-primary-hover) !important;
            border-color: var(--gh-primary-hover) !important;
            color: #ffffff !important;
            transform: translateY(-1px);
        }
        [data-bs-theme="dark"] .btn-danger,
        [data-bs-theme="dark"] .btn-primary {
            color: #07101f !important;
        }
        .btn-outline-danger {
            color: var(--gh-primary) !important;
            border-color: var(--gh-primary) !important;
        }
        .btn-outline-danger:hover {
            background-color: var(--gh-primary-hover) !important;
            border-color: var(--gh-primary-hover) !important;
            color: #ffffff !important;
        }
        .btn-dark {
            background-color: var(--gh-primary) !important;
            border-color: var(--gh-primary) !important;
            color: #ffffff !important;
        }
        .btn-dark:hover {
            background-color: var(--gh-primary-hover) !important;
            border-color: var(--gh-primary-hover) !important;
            color: #ffffff !important;
            transform: translateY(-1px);
        }
        [data-bs-theme="dark"] .btn-dark {
            background-color: var(--gh-primary) !important;
            border-color: var(--gh-primary) !important;
            color: #ffffff !important;
        }
        [data-bs-theme="dark"] .btn-dark:hover {
            background-color: var(--gh-primary-hover) !important;
            border-color: var(--gh-primary-hover) !important;
            color: #ffffff !important;
        }
        .btn-outline-dark {
            border-color: var(--gh-border) !important;
            color: var(--bs-body-color) !important;
        }
        .btn-outline-dark:hover {
            background-color: var(--gh-primary-hover) !important;
            border-color: var(--gh-primary-hover) !important;
            color: #ffffff !important;
        }
        [data-bs-theme="dark"] .btn-outline-dark:hover {
            background-color: var(--gh-primary-hover) !important;
            border-color: var(--gh-primary-hover) !important;
            color: #ffffff !important;
        }
        .btn-outline-light:hover {
            background-color: var(--gh-accent) !important;
            border-color: var(--gh-accent) !important;
            color: #07101f !important;
        }

        /* Form Controls */
        .form-control, .form-select {
            border: 1px solid var(--gh-border);
            border-radius: var(--gh-radius-sm);
            background-color: var(--gh-card-bg);
            color: var(--bs-body-color);
            padding: 0.55rem 0.85rem;
            font-size: 0.95rem;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--gh-accent);
            box-shadow: 0 0 0 3px rgba(0, 200, 255, 0.2);
            background-color: var(--gh-card-bg);
            color: var(--bs-body-color);
        }

        /* Badges */
        .badge {
            font-weight: 600;
            letter-spacing: 0.02em;
            padding: 0.35em 0.65em;
            border-radius: 6px;
        }
        .badge-grade {
            background-color: var(--gh-primary);
            color: #ffffff;
            font-size: 0.72rem;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }
        [data-bs-theme="dark"] .badge-grade {
            background-color: var(--gh-secondary);
            color: #ffffff;
        }
        .dropdown-menu {
            background-color: var(--gh-card-bg);
            border: 1px solid var(--gh-border) !important;
            box-shadow: var(--gh-shadow);
        }
        .dropdown-item {
            color: var(--bs-body-color);
            transition: background-color 0.18s ease, color 0.18s ease;
        }
        .dropdown-item:hover,
        .dropdown-item:focus {
            background-color: var(--gh-accent);
            color: #07101f;
        }
        .dropdown-item.active,
        .dropdown-item:active {
            background-color: var(--gh-soft);
            color: #07101f;
        }

        /* Modern Navbar */
        .gh-navbar {
            background: rgba(7, 11, 22, 0.96) !important;
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            transition: all 0.3s ease;
            z-index: 1030;
        }
        .gh-brand {
            font-weight: 800;
            letter-spacing: 0.04em;
            font-size: 1.75rem;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            text-decoration: none;
            color: #ffffff !important;
        }
        .gh-brand-badge {
            background: var(--gh-accent);
            color: #07101f;
            font-size: 0.62rem;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 4px;
            letter-spacing: 0.08em;
        }
        .gh-nav-link {
            color: rgba(255, 255, 255, 0.8) !important;
            font-weight: 500;
            font-size: 0.9rem;
            padding: 0.5rem 0.85rem !important;
            border-radius: var(--gh-radius-sm);
            transition: all 0.2s ease;
        }
        .gh-nav-link:hover {
            color: #07101f !important;
            background: var(--gh-accent);
        }
        .gh-nav-link.active {
            color: #07101f !important;
            background: var(--gh-soft);
        }
        .gh-commitments {
            background: rgba(7, 11, 22, 0.98);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            padding: 0.5rem 1rem;
        }
        .commit-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 0.85rem;
            border-radius: 9999px;
            background-color: var(--gh-card-bg);
            border: 1px solid var(--gh-border);
            font-size: 0.83rem;
            font-weight: 500;
            color: var(--bs-body-color);
            box-shadow: var(--gh-shadow-sm);
        }
        .commit-badge i {
            color: var(--gh-accent);
            font-size: 1rem;
        }

        /* Alerts */
        .alert {
            border-radius: var(--gh-radius-sm);
            border: 0;
            border-left: 4px solid transparent;
            box-shadow: var(--gh-shadow-sm);
        }
        .alert-success {
            background-color: #e8fff5;
            color: #075c42;
            border-left-color: #00c98b;
        }
        [data-bs-theme="dark"] .alert-success {
            background-color: rgba(0, 201, 139, 0.16);
            color: #59ffc6;
        }
        .alert-danger {
            background-color: rgba(201, 0, 131, 0.12);
            color: #8d005c;
            border-left-color: var(--gh-alert);
        }
        [data-bs-theme="dark"] .alert-danger {
            background-color: rgba(255, 43, 214, 0.16);
            color: #ff8be9;
        }
        .alert-warning {
            background-color: rgba(154, 184, 0, 0.14);
            color: #536400;
            border-left-color: var(--gh-primary-hover);
        }
        [data-bs-theme="dark"] .alert-warning {
            background-color: rgba(183, 255, 0, 0.14);
            color: #d7ff70;
        }

        /* Nút nổi góc phải */
        .float-btn {
            width: 44px; height: 44px; border-radius: 50%; border: 0; padding: 0;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 1.15rem; text-decoration: none;
            box-shadow: 0 4px 14px rgba(0,0,0,.25); cursor: pointer;
            transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), filter 0.2s ease;
        }
        .float-btn:hover {
            color: #07101f;
            background: var(--gh-accent) !important;
            transform: translateY(-2px) scale(1.06);
            filter: none;
        }

        /* Card Product Hover Actions (Popup Thêm vào giỏ hàng & Xem nhanh) */
        .product-media-wrapper {
            position: relative;
        }
        .product-hover-overlay {
            opacity: 0;
            visibility: hidden;
            transform: translate(-50%, -40%) scale(0.92);
            transition: opacity 0.22s cubic-bezier(0.16, 1, 0.3, 1), transform 0.22s cubic-bezier(0.16, 1, 0.3, 1), visibility 0.22s;
            pointer-events: none;
            z-index: 10;
        }
        .card-product:hover .product-hover-overlay {
            opacity: 1;
            visibility: visible;
            transform: translate(-50%, -50%) scale(1);
            pointer-events: auto;
        }
        .btn-hover-action {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.45rem 0.85rem;
            font-size: 0.78rem;
            font-weight: 700;
            border-radius: 9999px;
            border: 2px solid #ffffff !important;
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.3);
            text-decoration: none;
            cursor: pointer;
            transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s ease, filter 0.2s ease;
        }
        .btn-hover-action:hover {
            transform: translateY(-2px) scale(1.06);
        }
        /* Ô màu vàng: Thêm vào giỏ hàng */
        .btn-hover-yellow {
            background-color: var(--gh-soft) !important;
            color: #07101f !important;
        }
        .btn-hover-yellow:hover {
            background-color: var(--gh-accent) !important;
            color: #07101f !important;
        }
        /* Ô màu hồng: Xem nhanh trang chi tiết */
        .btn-hover-pink {
            background-color: var(--gh-alert) !important;
            color: #ffffff !important;
        }
        .btn-hover-pink:hover {
            background-color: var(--gh-soft) !important;
            color: #07101f !important;
        }
        a.text-muted:hover,
        a.text-reset:hover {
            color: var(--gh-primary-hover) !important;
        }

        /* Khi in / lưu PDF */
        @media print {
            nav.navbar, footer, .no-print, .position-fixed { display: none !important; }
            body { background: #fff !important; color: #000 !important; }
            .card { box-shadow: none !important; border-color: var(--gh-border) !important; }
        }
    </style>
    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100">

{{-- Thanh điều hướng chuẩn hiện diện xuyên suốt --}}
<nav class="navbar navbar-expand-lg gh-navbar sticky-top">
    <div class="container">
        <a class="navbar-brand gh-brand" href="{{ route('home') }}">
            <span>GUNPLA</span><span class="text-danger">HUB</span>
            <span class="gh-brand-badge">STORE</span>
        </a>
        <button class="navbar-toggler border-0 text-white" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            <i class="bi bi-list fs-2"></i>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 align-items-lg-center">
                <li class="nav-item">
                    <a class="nav-link gh-nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                        <i class="bi bi-house-door me-1"></i>Trang chủ
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link gh-nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">
                        <i class="bi bi-grid me-1"></i>Cửa hàng
                    </a>
                </li>
                @unless($isAdmin)
                    <li class="nav-item">
                        <a class="nav-link gh-nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">
                            <i class="bi bi-chat-dots me-1"></i>Liên hệ
                        </a>
                    </li>
                @endunless
                @if($isAdmin)
                    {{-- 1. Hàng hóa (dropdown: sản phẩm, danh mục, thương hiệu) --}}
                    @php
                        $isHangHoaActive = request()->routeIs('admin.products.*', 'admin.categories.*', 'admin.brands.*');
                    @endphp
                    <li class="nav-item dropdown">
                        <a class="nav-link gh-nav-link dropdown-toggle {{ $isHangHoaActive ? 'active fw-bold' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-boxes me-1"></i>Hàng hóa
                        </a>
                        <ul class="dropdown-menu shadow border-0" style="border-radius: 12px; font-size: 0.9rem;">
                            <li>
                                <a class="dropdown-item py-2 {{ request()->routeIs('admin.products.*') ? 'active fw-bold' : '' }}" href="{{ route('admin.products.index') }}">
                                    <i class="bi bi-box-seam me-2"></i>Sản phẩm
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-2 {{ request()->routeIs('admin.categories.*') ? 'active fw-bold' : '' }}" href="{{ route('admin.categories.index') }}">
                                    <i class="bi bi-tags me-2"></i>Danh mục
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-2 {{ request()->routeIs('admin.brands.*') ? 'active fw-bold' : '' }}" href="{{ route('admin.brands.index') }}">
                                    <i class="bi bi-award me-2"></i>Thương hiệu
                                </a>
                            </li>
                        </ul>
                    </li>

                    {{-- 2. Con người (dropdown: khách hàng, đơn hàng, tin nhắn) --}}
                    @php
                        $pendingOrders = \App\Models\Order::needsAction()->count();
                        $isConNguoiActive = request()->routeIs('admin.customers.*', 'admin.orders.*', 'admin.contacts.*');
                    @endphp
                    <li class="nav-item dropdown">
                        <a class="nav-link gh-nav-link dropdown-toggle {{ $isConNguoiActive ? 'active fw-bold' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-people me-1"></i>Con người
                            @if($pendingOrders > 0)
                                <span class="badge rounded-pill bg-danger ms-1" style="font-size: 0.68rem; padding: 2px 6px;">{{ $pendingOrders }}</span>
                            @endif
                        </a>
                        <ul class="dropdown-menu shadow border-0" style="border-radius: 12px; font-size: 0.9rem;">
                            <li>
                                <a class="dropdown-item py-2 {{ request()->routeIs('admin.customers.*') ? 'active fw-bold' : '' }}" href="{{ route('admin.customers.index') }}">
                                    <i class="bi bi-person-lines-fill me-2"></i>Khách hàng
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-2 d-flex align-items-center justify-content-between {{ request()->routeIs('admin.orders.*') ? 'active fw-bold' : '' }}" href="{{ route('admin.orders.index') }}">
                                    <span><i class="bi bi-receipt me-2"></i>Đơn hàng</span>
                                    @if($pendingOrders > 0)
                                        <span class="badge rounded-pill bg-danger" style="font-size: 0.7rem;">{{ $pendingOrders }}</span>
                                    @endif
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-2 {{ request()->routeIs('admin.contacts.*') ? 'active fw-bold' : '' }}" href="{{ route('admin.contacts.index') }}">
                                    <i class="bi bi-chat-dots me-2"></i>Tin nhắn
                                </a>
                            </li>
                        </ul>
                    </li>

                    {{-- 3. Mã giảm giá --}}
                    <li class="nav-item">
                        <a class="nav-link gh-nav-link {{ request()->routeIs('admin.vouchers.*') ? 'active' : '' }}" href="{{ route('admin.vouchers.index') }}">
                            <i class="bi bi-ticket-perforated me-1"></i>Mã giảm giá
                        </a>
                    </li>

                    {{-- 4. Báo cáo (giữ nguyên: Báo cáo doanh thu, Báo cáo tồn kho) --}}
                    @php
                        $isBaoCaoActive = request()->routeIs('admin.reports.*');
                    @endphp
                    <li class="nav-item dropdown">
                        <a class="nav-link gh-nav-link dropdown-toggle {{ $isBaoCaoActive ? 'active fw-bold' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-graph-up me-1"></i>Báo cáo
                        </a>
                        <ul class="dropdown-menu shadow border-0" style="border-radius: 12px; font-size: 0.9rem;">
                            <li><a class="dropdown-item py-2 {{ request()->routeIs('admin.reports.revenue') ? 'active fw-bold' : '' }}" href="{{ route('admin.reports.revenue') }}"><i class="bi bi-graph-up me-2"></i>Báo cáo doanh thu</a></li>
                            <li><a class="dropdown-item py-2 {{ request()->routeIs('admin.reports.inventory') ? 'active fw-bold' : '' }}" href="{{ route('admin.reports.inventory') }}"><i class="bi bi-boxes me-2"></i>Báo cáo tồn kho</a></li>
                        </ul>
                    </li>

                    {{-- 5. Nhật ký --}}
                    <li class="nav-item">
                        <a class="nav-link gh-nav-link {{ request()->routeIs('admin.audit.*') ? 'active' : '' }}" href="{{ route('admin.audit.index') }}">
                            <i class="bi bi-journal-text me-1"></i>Nhật ký
                        </a>
                    </li>
                @endif
            </ul>

            @include('layouts._tools')
        </div>
    </div>
</nav>

{{-- Cam kết của cửa hàng --}}
    <div class="gh-commitments d-flex flex-wrap justify-content-center align-items-center gap-2">
        @foreach(config('shop.commitments') as $commitment)
            <span class="commit-badge">
                <i class="bi {{ $commitment['icon'] }}"></i>{{ $commitment['text'] }}
            </span>
        @endforeach
    </div>
    
<main class="container py-4 flex-grow-1">
    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
            <i class="bi bi-info-circle-fill me-2 fs-5"></i>
            <div>{{ session('warning') }}</div>
        </div>
    @endif

    @yield('content')
</main>

{{-- Footer phong cách Japanese Clean Store --}}
<footer class="mt-auto pt-5 pb-4 border-top" style="background-color: var(--gh-card-bg); border-color: var(--gh-border) !important;">
    <div class="container">
        <div class="row g-4 mb-4">
            <div class="col-lg-4 col-md-6">
                <a class="gh-brand mb-3 d-inline-flex" href="{{ route('home') }}">
                    <span class="text-body">GUNPLA</span><span class="text-danger">HUB</span>
                    <span class="gh-brand-badge">STORE</span>
                </a>
                <p class="text-muted small mb-3 lh-base">
                    GunplaHub chuyên cung cấp các dòng mô hình Gundam chính hãng Bandai Nhật Bản, dụng cụ và phụ kiện cao cấp cho Builder tại Việt Nam.
                </p>
                <div class="small text-muted mb-2">
                    <i class="bi bi-geo-alt-fill text-danger me-2"></i>{{ config('shop.address') ?: 'Hệ thống phân phối Gunpla chính hãng toàn quốc' }}
                </div>
                <div class="small text-muted mb-2">
                    <i class="bi bi-telephone-fill text-danger me-2"></i>Hotline: <a href="tel:{{ $phoneDigits }}" class="text-reset fw-semibold">{{ $shopPhone }}</a>
                </div>
                <div class="small text-muted">
                    <i class="bi bi-shield-check text-success me-2"></i>100% Sản phẩm phân phối chính hãng
                </div>
            </div>

            <div class="col-lg-2 col-md-6">
                <h6 class="fw-bold text-uppercase small mb-3" style="letter-spacing: 0.05em;">Dòng mô hình</h6>
                <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
                    <li><a href="{{ route('products.index', ['grade' => 'HG']) }}" class="text-decoration-none text-muted">High Grade (HG 1/144)</a></li>
                    <li><a href="{{ route('products.index', ['grade' => 'RG']) }}" class="text-decoration-none text-muted">Real Grade (RG 1/144)</a></li>
                    <li><a href="{{ route('products.index', ['grade' => 'MG']) }}" class="text-decoration-none text-muted">Master Grade (MG 1/100)</a></li>
                    <li><a href="{{ route('products.index', ['grade' => 'PG']) }}" class="text-decoration-none text-muted">Perfect Grade (PG 1/60)</a></li>
                    <li><a href="{{ route('products.index', ['grade' => 'SD']) }}" class="text-decoration-none text-muted">SD & BB Senshi</a></li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-6">
                <h6 class="fw-bold text-uppercase small mb-3" style="letter-spacing: 0.05em;">Hỗ trợ khách hàng</h6>
                <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
                    <li><a href="{{ route('products.index') }}" class="text-decoration-none text-muted">Tất cả sản phẩm</a></li>
                    <li><a href="{{ route('contact') }}" class="text-decoration-none text-muted">Gửi yêu cầu hỗ trợ</a></li>
                    <li><a href="{{ route('orders.index') }}" class="text-decoration-none text-muted">Tra cứu hành trình đơn hàng</a></li>
                    <li><a href="{{ route('contact') }}" class="text-decoration-none text-muted">Chính sách bảo hành & hoàn tiền</a></li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-6">
                <h6 class="fw-bold text-uppercase small mb-3" style="letter-spacing: 0.05em;">Phương thức thanh toán</h6>
                <p class="text-muted small mb-3">Hỗ trợ đa dạng phương thức thanh toán an toàn, tiện lợi.</p>
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <span class="badge border bg-body-tertiary text-body fw-normal px-2 py-1"><i class="bi bi-cash-stack me-1"></i>Tiền mặt (COD)</span>
                    <span class="badge border bg-body-tertiary text-body fw-normal px-2 py-1"><i class="bi bi-qr-code me-1"></i>VietQR Pay</span>
                    <span class="badge border bg-body-tertiary text-body fw-normal px-2 py-1"><i class="bi bi-credit-card me-1"></i>VNPay</span>
                </div>
                <div class="p-2 border rounded-3 bg-body-tertiary small text-muted">
                    <i class="bi bi-box-seam text-danger me-1"></i>Đóng gói 3 lớp chống móp hộp khi vận chuyển xa.
                </div>
            </div>
        </div>

        <div class="border-top pt-3 d-flex flex-wrap justify-content-between align-items-center small text-muted" style="border-color: var(--gh-border) !important;">
            <div>&copy; {{ date('Y') }} {{ config('shop.name') }}. All rights reserved.</div>
            <div>Thiết kế theo chuẩn Clean & Premium Japanese Storefront</div>
        </div>
    </div>
</footer>

{{-- Nút nổi: về đầu trang, gọi điện, Zalo --}}
<div class="position-fixed d-flex flex-column gap-2" style="right: 18px; bottom: 18px; z-index: 1040;">
    <button type="button" id="backToTop" class="float-btn" style="background: var(--gh-primary); display: none;" title="Về đầu trang" aria-label="Về đầu trang">
        <i class="bi bi-chevron-up"></i>
    </button>
    @unless($isAdmin)
    <a href="tel:{{ $phoneDigits }}" class="float-btn" style="background: var(--gh-primary);" title="Gọi {{ $shopPhone }}" aria-label="Gọi điện thoại">
        <i class="bi bi-telephone-fill"></i>
    </a>
    <a href="https://zalo.me/{{ $phoneDigits }}" target="_blank" rel="noopener" class="float-btn"
       style="background: #0068ff; font-size: .8rem; font-weight: 700;" title="Chat Zalo" aria-label="Chat Zalo">Zalo</a>
    @endunless
</div>

{{-- Modal Xem nhanh sản phẩm (Quick View) --}}
<div class="modal fade" id="quickViewModal" tabindex="-1" aria-labelledby="quickViewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: var(--gh-radius); overflow: hidden;">
            <div class="modal-header border-0 pb-0 d-flex justify-content-between align-items-center">
                <span class="badge bg-danger text-white fw-bold px-2 py-1 small" id="qvBrand">BANDAI</span>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body p-4 pt-2">
                <div class="row g-4 align-items-center">
                    <div class="col-md-5 text-center">
                        <div class="border rounded-3 p-3 bg-white d-flex align-items-center justify-content-center" style="height: 290px;">
                            <img id="qvImage" src="" alt="" class="img-fluid object-fit-contain" style="max-height: 270px; width: 100%;">
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-dark text-white fw-bold" id="qvGrade"></span>
                            <span class="badge bg-light text-secondary border fw-normal" id="qvScale"></span>
                            <span class="badge bg-secondary-subtle text-secondary" id="qvSeries"></span>
                        </div>
                        <h4 class="fw-bold mb-2" id="qvName" style="font-size: 1.25rem; line-height: 1.35;"></h4>
                        <div class="small text-muted mb-3">Mã sản phẩm: <code class="fw-bold text-danger" id="qvSku"></code></div>
                        
                        <div class="p-3 rounded-3 bg-body-tertiary mb-3 border d-flex align-items-center justify-content-between" style="border-color: var(--gh-border) !important;">
                            <div>
                                <div class="small text-muted text-uppercase fw-semibold" style="font-size: 0.72rem;">Giá bán chính hãng</div>
                                <div class="text-danger fw-bold fs-3" id="qvPrice"></div>
                            </div>
                            <div id="qvStockBadge"></div>
                        </div>

                        <form method="POST" action="{{ route('cart.add') }}" class="mb-3" id="qvForm">
                            @csrf
                            <input type="hidden" name="sku" id="qvInputSku" value="">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <div class="input-group" style="width: 120px;">
                                    <span class="input-group-text bg-body-tertiary border-end-0 small text-muted fw-bold">SL</span>
                                    <input type="number" name="quantity" id="qvQuantity" value="1" min="1" max="99" class="form-control text-center fw-bold">
                                </div>
                                <button type="submit" name="buy_now" value="0" class="btn btn-outline-danger fw-bold rounded-pill flex-grow-1 py-2">
                                    <i class="bi bi-cart-plus-fill me-1"></i>Thêm vào giỏ
                                </button>
                            </div>
                            <button type="submit" name="buy_now" value="1" class="btn btn-danger w-100 fw-bold rounded-pill shadow-sm py-2" style="font-size: 1rem;">
                                <i class="bi bi-bag-check-fill me-1"></i>Tiến hành mua hàng luôn
                            </button>
                        </form>

                        <div class="text-end pt-2 border-top" style="border-color: var(--gh-border) !important;">
                            <a href="#" id="qvDetailLink" class="small text-muted text-decoration-none fw-semibold">
                                Xem toàn bộ trang chi tiết sản phẩm <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    (function () {
        // Đổi giao diện sáng/tối: chỉ hiển thị icon tròn nhỏ
        var btn = document.getElementById('themeToggle');
        var root = document.documentElement;

        function render() {
            if (!btn) { return; }
            var dark = root.getAttribute('data-bs-theme') === 'dark';
            btn.innerHTML = dark ? '<i class="bi bi-sun-fill text-warning"></i>' : '<i class="bi bi-moon-stars-fill text-white"></i>';
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

        // 1. Xem nhanh chi tiết sản phẩm (Quick View Modal)
        document.addEventListener('click', function (e) {
            var qvBtn = e.target.closest('.btn-quick-view');
            if (!qvBtn) return;

            var sku = qvBtn.dataset.sku;
            var name = qvBtn.dataset.name;
            var price = qvBtn.dataset.price;
            var image = qvBtn.dataset.image;
            var brand = qvBtn.dataset.brand;
            var grade = qvBtn.dataset.grade;
            var scale = qvBtn.dataset.scale;
            var series = qvBtn.dataset.series;
            var status = qvBtn.dataset.status;
            var stock = parseInt(qvBtn.dataset.stock, 10) || 0;
            var detailUrl = qvBtn.dataset.detailUrl;

            document.getElementById('qvBrand').textContent = brand || 'BANDAI';
            document.getElementById('qvName').textContent = name;
            document.getElementById('qvSku').textContent = sku;
            document.getElementById('qvPrice').textContent = price;
            document.getElementById('qvInputSku').value = sku;
            document.getElementById('qvDetailLink').href = detailUrl;

            var imgEl = document.getElementById('qvImage');
            if (image) {
                imgEl.src = image;
                imgEl.style.display = 'block';
            } else {
                imgEl.src = '';
                imgEl.style.display = 'none';
            }

            var gradeEl = document.getElementById('qvGrade');
            gradeEl.textContent = grade;
            gradeEl.style.display = grade ? 'inline-block' : 'none';

            var scaleEl = document.getElementById('qvScale');
            scaleEl.textContent = scale;
            scaleEl.style.display = scale ? 'inline-block' : 'none';

            var seriesEl = document.getElementById('qvSeries');
            seriesEl.textContent = series;
            seriesEl.style.display = series ? 'inline-block' : 'none';

            var stockBadgeEl = document.getElementById('qvStockBadge');
            if (status === 'Còn hàng' && stock > 0) {
                if (stock <= 5) {
                    stockBadgeEl.innerHTML = '<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1">Còn ' + stock + '</span>';
                } else {
                    stockBadgeEl.innerHTML = '<span class="badge bg-success-subtle text-success-emphasis border border-success-subtle px-2 py-1">Còn hàng</span>';
                }
                document.getElementById('qvForm').style.display = 'block';
                document.getElementById('qvQuantity').max = stock;
            } else {
                stockBadgeEl.innerHTML = '<span class="badge bg-secondary-subtle text-secondary border px-2 py-1">Hết hàng</span>';
                document.getElementById('qvForm').style.display = 'none';
            }
        });

        // 2. Nút ẩn / hiện mật khẩu
        document.addEventListener('click', function (e) {
            var toggleBtn = e.target.closest('.btn-toggle-password');
            if (!toggleBtn) return;

            var inputGroup = toggleBtn.closest('.input-group');
            if (!inputGroup) return;

            var input = inputGroup.querySelector('input');
            if (!input) return;

            var icon = toggleBtn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                if (icon) {
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                }
            } else {
                input.type = 'password';
                if (icon) {
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                }
            }
        });

        // 3. Kiểm tra ràng buộc thời gian thực các trường nhập (Real-time Constraint Validation)
        function validateField(field) {
            var form = field.closest('.needs-constraint-validation');
            if (!form) return;

            var val = field.value;
            var name = field.name;
            var type = field.type;
            var isRequired = field.hasAttribute('required');

            var isValid = true;
            var errorMsg = '';
            var successMsg = 'Hợp lệ';

            // Không kiểm tra các trường ẩn hoặc checkbox
            if (type === 'hidden' || type === 'checkbox' || type === 'radio') return;

            // Kiểm tra rỗng đối với trường bắt buộc
            if (isRequired && val.trim() === '') {
                isValid = false;
                errorMsg = 'Trường này không được để trống.';
            } else if (val.trim() !== '') {
                // Kiểm tra Họ tên
                if (name === 'full_name' || name === 'name' || name === 'receiver_name') {
                    if (val.trim().length < 2) {
                        isValid = false;
                        errorMsg = 'Họ và tên phải từ 2 ký tự trở lên.';
                    }
                }
                // Kiểm tra Email
                else if (type === 'email' || name === 'email') {
                    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(val.trim())) {
                        isValid = false;
                        errorMsg = 'Email không đúng định dạng (VD: builder@example.com).';
                    }
                }
                // Kiểm tra Số điện thoại
                else if (type === 'tel' || name === 'phone' || name === 'receiver_phone') {
                    var cleanPhone = val.replace(/[\s.-]/g, '');
                    var phoneRegex = /^(0|\+84)[0-9]{9,10}$/;
                    if (!phoneRegex.test(cleanPhone)) {
                        isValid = false;
                        errorMsg = 'Số điện thoại phải từ 10 số (VD: 0912345678).';
                    }
                }
                // Kiểm tra Mật khẩu
                else if (type === 'password' && (name === 'password' || name === 'new_password')) {
                    if (val.length < 8) {
                        isValid = false;
                        errorMsg = 'Mật khẩu phải tối thiểu 8 ký tự.';
                    }
                }
                // Kiểm tra Xác nhận mật khẩu
                else if (name === 'password_confirmation') {
                    var pwdInput = form.querySelector('input[name="password"]') || form.querySelector('input[name="new_password"]');
                    if (pwdInput && val !== pwdInput.value) {
                        isValid = false;
                        errorMsg = 'Mật khẩu nhập lại không khớp.';
                    }
                }
                // Kiểm tra Địa chỉ
                else if (name === 'shipping_address' || name === 'address') {
                    if (val.trim().length < 6) {
                        isValid = false;
                        errorMsg = 'Địa chỉ cần chi tiết hơn (tối thiểu 6 ký tự).';
                    }
                }
                // Kiểm tra Tin nhắn liên hệ
                else if (name === 'message') {
                    if (val.trim().length < 10) {
                        isValid = false;
                        errorMsg = 'Nội dung tin nhắn tối thiểu 10 ký tự.';
                    }
                }
            }

            // Cập nhật trạng thái hiển thị
            var container = field.closest('.input-group') || field;
            var feedback = container.parentElement.querySelector('.dynamic-feedback');
            if (!feedback) {
                feedback = document.createElement('div');
                feedback.className = 'dynamic-feedback small mt-1';
                container.parentElement.appendChild(feedback);
            }

            if (isValid) {
                field.classList.remove('is-invalid');
                field.classList.add('is-valid');
                feedback.className = 'dynamic-feedback small mt-1 text-success';
                feedback.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i>' + successMsg;
            } else {
                field.classList.remove('is-valid');
                field.classList.add('is-invalid');
                feedback.className = 'dynamic-feedback small mt-1 text-danger';
                feedback.innerHTML = '<i class="bi bi-exclamation-circle-fill me-1"></i>' + errorMsg;
            }

            return isValid;
        }

        // Lắng nghe sự kiện gõ phím và rời ô nhập
        document.addEventListener('input', function (e) {
            if (e.target.matches('.needs-constraint-validation input, .needs-constraint-validation textarea')) {
                validateField(e.target);
            }
        });

        document.addEventListener('blur', function (e) {
            if (e.target.matches('.needs-constraint-validation input, .needs-constraint-validation textarea')) {
                validateField(e.target);
            }
        }, true);

        // Chặn gửi form nếu còn trường không hợp lệ
        document.addEventListener('submit', function (e) {
            var form = e.target.closest('.needs-constraint-validation');
            if (!form) return;

            var fields = form.querySelectorAll('input:not([type="hidden"]):not([type="submit"]):not([type="button"]), textarea');
            var formValid = true;

            fields.forEach(function (field) {
                var valid = validateField(field);
                if (valid === false) {
                    formValid = false;
                }
            });

            if (!formValid) {
                e.preventDefault();
                e.stopPropagation();
                var firstInvalid = form.querySelector('.is-invalid');
                if (firstInvalid) {
                    firstInvalid.focus();
                }
            }
        });
    })();
</script>
@stack('scripts')
</body>
</html>

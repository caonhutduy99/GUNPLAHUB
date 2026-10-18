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
    </style>
</head>
<body class="bg-body-tertiary">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('home') }}">GunplaHub</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Trang chủ</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('products.index') }}">Cửa hàng</a></li>
                @auth
                    @if(auth()->user()->isAdmin())
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Quản trị</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.categories.index') }}">Danh mục</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.products.index') }}">Sản phẩm</a></li>
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

            <ul class="navbar-nav">
                @guest
                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Đăng nhập</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Đăng ký</a></li>
                @else
                    <li class="nav-item d-flex align-items-center text-white me-3">
                        Xin chào, {{ auth()->user()->full_name }}
                    </li>
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-light btn-sm mt-1">Đăng xuất</button>
                        </form>
                    </li>
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

    @yield('content')
</main>

<footer class="text-center text-muted py-4 small">
    &copy; {{ date('Y') }} GunplaHub - Đồ án môn học
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    (function () {
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
    })();
</script>
</body>
</html>
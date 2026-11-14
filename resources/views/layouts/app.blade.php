@php
    $shopPhone = config('shop.phone');
    $phoneDigits = preg_replace('/\D/', '', $shopPhone);
    $isAdmin = auth()->check() && auth()->user()->isAdmin();
    $pendingOrders = $isAdmin ? \App\Models\Order::needsAction()->count() : 0;
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

    {{-- Font: Be Vietnam Pro--}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/gunplahub.css') }}?v={{ filemtime(public_path('css/gunplahub.css')) }}" rel="stylesheet">
    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100 {{ $isAdmin ? 'gh-is-admin' : '' }}">

{{-- Thanh điều hướng --}}
<nav class="navbar navbar-expand-lg gh-navbar sticky-top">
    <div class="{{ $isAdmin ? 'container-fluid px-3 px-lg-4' : 'container' }}">
        @if($isAdmin)
            <button class="navbar-toggler d-lg-none me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminSidebar" aria-controls="adminSidebar" aria-label="Mở menu quản trị">
                <x-icon name="menu" size="1.25rem" />
            </button>
        @endif
        <a class="navbar-brand gh-brand me-3" href="{{ route('home') }}">
            <span><strong>Gunpla</strong>Hub</span>
            @if($isAdmin)<span class="gh-brand-sub">Quản trị</span>@endif
        </a>

        @if($isAdmin)
            <div class="ms-auto">
                @include('layouts._tools')
            </div>
        @else
            <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Mở menu">
                <x-icon name="menu" size="1.25rem" />
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 align-items-lg-center">
                    <li class="nav-item">
                        <a class="nav-link gh-nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Trang chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link gh-nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">Cửa hàng</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link gh-nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">Liên hệ</a>
                    </li>
                </ul>
                @include('layouts._tools')
            </div>
        @endif
    </div>
</nav>

@unless($isAdmin)
    {{-- Cam kết của cửa hàng --}}
    <div class="gh-commitments d-flex flex-wrap justify-content-center align-items-center">
        @foreach(config('shop.commitments') as $commitment)
            <span class="commit-badge">
                <x-icon name="{{ $commitment['icon'] }}" />{{ $commitment['text'] }}
            </span>
        @endforeach
    </div>
@endunless

@if($isAdmin)
<div class="gh-shell">
    {{-- Thanh bên quản trị: mục đang chọn có thanh nhấn bên trái --}}
    <aside class="gh-sidebar offcanvas-lg offcanvas-start" tabindex="-1" id="adminSidebar" aria-label="Menu quản trị">
        <div class="offcanvas-header d-lg-none">
            <span class="gh-brand"><span><strong>Gunpla</strong>Hub</span><span class="gh-brand-sub">Quản trị</span></span>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#adminSidebar" aria-label="Đóng"></button>
        </div>
        <div class="offcanvas-body d-block py-2 px-0">
            <nav class="d-flex flex-column" aria-label="Quản trị">
                <a class="gh-side-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                    <x-icon name="layout-grid" />Tổng quan
                </a>

                <div class="gh-side-group">Hàng hóa</div>
                <a class="gh-side-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">
                    <x-icon name="package-open" />Sản phẩm
                </a>
                <a class="gh-side-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
                    <x-icon name="list" />Danh mục
                </a>
                <a class="gh-side-link {{ request()->routeIs('admin.brands.*') ? 'active' : '' }}" href="{{ route('admin.brands.index') }}">
                    <x-icon name="badge-check" />Thương hiệu
                </a>

                <div class="gh-side-group">Bán hàng</div>
                <a class="gh-side-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}">
                    <x-icon name="receipt" />Đơn hàng
                    @if($pendingOrders > 0)<span class="badge rounded-pill bg-danger">{{ $pendingOrders }}</span>@endif
                </a>
                <a class="gh-side-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}" href="{{ route('admin.customers.index') }}">
                    <x-icon name="user" />Khách hàng
                </a>
                <a class="gh-side-link {{ request()->routeIs('admin.vouchers.*') ? 'active' : '' }}" href="{{ route('admin.vouchers.index') }}">
                    <x-icon name="ticket" />Mã giảm giá
                </a>
                <a class="gh-side-link {{ request()->routeIs('admin.contacts.*') ? 'active' : '' }}" href="{{ route('admin.contacts.index') }}">
                    <x-icon name="mail" />Tin nhắn
                </a>

                <div class="gh-side-group">Báo cáo</div>
                <a class="gh-side-link {{ request()->routeIs('admin.reports.revenue') ? 'active' : '' }}" href="{{ route('admin.reports.revenue') }}">
                    <x-icon name="circle-dollar-sign" />Doanh thu
                </a>
                <a class="gh-side-link {{ request()->routeIs('admin.reports.inventory') ? 'active' : '' }}" href="{{ route('admin.reports.inventory') }}">
                    <x-icon name="box" />Tồn kho
                </a>

                <div class="gh-side-group">Hệ thống</div>
                <a class="gh-side-link {{ request()->routeIs('admin.audit.*') ? 'active' : '' }}" href="{{ route('admin.audit.index') }}">
                    <x-icon name="settings" />Nhật ký hoạt động
                </a>
            </nav>
        </div>
    </aside>
@endif

<main class="{{ $isAdmin ? 'gh-admin-main' : 'container py-4' }} flex-grow-1">
    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
            <x-icon name="circle-check" class="me-2 fs-5" />
            <div>{{ session('success') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
            <x-icon name="triangle-alert" class="me-2 fs-5" />
            <div>{{ session('error') }}</div>
        </div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
            <x-icon name="info" class="me-2 fs-5" />
            <div>{{ session('warning') }}</div>
        </div>
    @endif

    @yield('content')
</main>
@if($isAdmin)
</div>
@endif

@unless($isAdmin)
{{-- Chân trang --}}
<footer class="gh-footer mt-auto pt-5 pb-4">
    <div class="container">
        <div class="row g-4 mb-4">
            <div class="col-lg-4 col-md-6">
                <a class="gh-brand mb-3" href="{{ route('home') }}"><span><strong>Gunpla</strong>Hub</span></a>
                <p class="text-muted small mb-3 lh-base">
                    GunplaHub chuyên cung cấp các dòng mô hình Gundam chính hãng Bandai Namco Nhật Bản, phụ kiện nâng cấp và dụng cụ lắp ráp cho Builder tại Việt Nam.
                </p>
                <div class="small text-muted mb-2">
                    <x-icon name="map-pin" class="text-accent me-2" />{{ config('shop.address') ?: 'Hệ thống phân phối Gunpla chính hãng toàn quốc' }}
                </div>
                <div class="small text-muted mb-2">
                    <x-icon name="phone" class="text-accent me-2" />Hotline: <a href="tel:{{ $phoneDigits }}" class="fw-semibold">{{ $shopPhone }}</a>
                </div>
                <div class="small text-muted">
                    <x-icon name="shield-check" class="text-accent me-2" />100% sản phẩm phân phối chính hãng
                </div>
            </div>

            <div class="col-lg-2 col-md-6">
                <h6 class="gh-foot-title">Dòng mô hình</h6>
                <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
                    <li><a href="{{ route('products.index', ['grade' => 'HG']) }}">High Grade (HG 1/144)</a></li>
                    <li><a href="{{ route('products.index', ['grade' => 'RG']) }}">Real Grade (RG 1/144)</a></li>
                    <li><a href="{{ route('products.index', ['grade' => 'MG']) }}">Master Grade (MG 1/100)</a></li>
                    <li><a href="{{ route('products.index', ['grade' => 'PG']) }}">Perfect Grade (PG 1/60)</a></li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-6">
                <h6 class="gh-foot-title">Hỗ trợ khách hàng</h6>
                <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
                    <li><a href="{{ route('products.index') }}">Tất cả sản phẩm</a></li>
                    <li><a href="{{ route('contact') }}">Gửi yêu cầu hỗ trợ</a></li>
                    <li><a href="{{ route('orders.index') }}">Tra cứu hành trình đơn hàng</a></li>
                    <li><a href="{{ route('contact') }}">Chính sách bảo hành &amp; hoàn tiền</a></li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-6">
                <h6 class="gh-foot-title">Phương thức thanh toán</h6>
                <p class="text-muted small mb-3">Hỗ trợ đa dạng phương thức thanh toán an toàn, tiện lợi.</p>
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <span class="badge border bg-body-tertiary text-body fw-normal px-2 py-1"><x-icon name="wallet" class="me-1" />Tiền mặt (COD)</span>
                    <span class="badge border bg-body-tertiary text-body fw-normal px-2 py-1"><x-icon name="qr-code" class="me-1" />VietQR</span>
                    <span class="badge border bg-body-tertiary text-body fw-normal px-2 py-1"><x-icon name="credit-card" class="me-1" />VNPay</span>
                </div>
                <div class="gh-package-note p-2 small">
                    <x-icon name="package-open" class="text-accent me-1" />Đóng gói 3 lớp chống móp hộp khi vận chuyển xa.
                </div>
            </div>
        </div>

        <div class="border-top pt-3 small text-muted">
            &copy; {{ date('Y') }} {{ config('shop.name') }}. All rights reserved.
        </div>
    </div>
</footer>
@endunless

{{-- Nút nổi: về đầu trang, gọi điện, Zalo --}}
<div class="position-fixed d-flex flex-column gap-2" style="right: 18px; bottom: 18px; z-index: 1040;">
    <button type="button" id="backToTop" class="float-btn" style="display: none;" title="Về đầu trang" aria-label="Về đầu trang">
        <x-icon name="chevron-up" />
    </button>
    @unless($isAdmin)
    <a href="tel:{{ $phoneDigits }}" class="float-btn" title="Gọi {{ $shopPhone }}" aria-label="Gọi điện thoại">
        <x-icon name="phone" />
    </a>
    <a href="https://zalo.me/{{ $phoneDigits }}" target="_blank" rel="noopener" class="float-btn zalo" title="Chat Zalo" aria-label="Chat Zalo">Zalo</a>
    @endunless
</div>

{{-- Modal Xem nhanh sản phẩm (Quick View) --}}
<div class="modal fade" id="quickViewModal" tabindex="-1" aria-labelledby="quickViewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0 d-flex justify-content-between align-items-center">
                <span class="badge bg-body-secondary text-body fw-semibold px-2 py-1" id="qvBrand">BANDAI</span>
                <button type="button" class="btn-close quick-view-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
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
                        <div class="small text-muted mb-3">Mã sản phẩm: <code class="product-sku fw-bold" id="qvSku"></code></div>
                        
                        <div class="p-3 rounded-3 bg-body-tertiary mb-3 border d-flex align-items-center justify-content-between" style="border-color: var(--gh-border) !important;">
                            <div>
                                <div class="small text-muted text-uppercase fw-semibold" style="font-size: 0.72rem;">Giá bán chính hãng</div>
                                <div class="product-price fw-bold fs-3" id="qvPrice"></div>
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
                                <button type="submit" name="buy_now" value="0" class="btn btn-outline-primary fw-semibold flex-grow-1 py-2">
                                    <x-icon name="shopping-cart" class="me-1" />Thêm vào giỏ
                                </button>
                            </div>
                            <button type="submit" name="buy_now" value="1" class="btn btn-primary w-100 fw-semibold py-2" style="font-size: 1rem;">
                                <x-icon name="shopping-cart" class="me-1" />Tiến hành mua hàng luôn
                            </button>
                        </form>

                        <div class="text-end pt-2 border-top" style="border-color: var(--gh-border) !important;">
                            <a href="#" id="qvDetailLink" class="small text-muted text-decoration-none fw-semibold">
                                Xem toàn bộ trang chi tiết sản phẩm <x-icon name="arrow-right" class="ms-1" />
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
            btn.classList.toggle('theme-dark', dark);
            btn.innerHTML = dark
                ? '<span class="gh-icon" style="--gh-icon-size: 1.15em; --gh-icon-url: url(\'/icons/sun.svg\');"></span>'
                : '<span class="gh-icon" style="--gh-icon-size: 1.15em; --gh-icon-url: url(\'/icons/moon.svg\');"></span>';
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
            var card = e.target.closest('.product-card-link');
            if (!card || e.target.closest('a, button, form, input, select, textarea')) {
                return;
            }

            window.location.href = card.dataset.productUrl;
        });

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
            document.getElementById('qvQuantity').value = '1';
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

            var icon = toggleBtn.querySelector('.gh-icon, i');
            if (input.type === 'password') {
                input.type = 'text';
                if (icon) {
                    if (icon.classList.contains('gh-icon')) {
                        icon.style.setProperty('--gh-icon-url', "url('/icons/eye-off.svg')");
                    } else {
                        icon.classList.remove('bi-eye');
                        icon.classList.add('bi-eye-slash');
                    }
                }
            } else {
                input.type = 'password';
                if (icon) {
                    if (icon.classList.contains('gh-icon')) {
                        icon.style.setProperty('--gh-icon-url', "url('/icons/eye.svg')");
                    } else {
                        icon.classList.remove('bi-eye-slash');
                        icon.classList.add('bi-eye');
                    }
                }
            }
        });

        function setFieldFeedback(field, isValid, message) {
            var container = field.closest('.input-group') || field;
            var feedback = container.parentElement.querySelector('.dynamic-feedback');
            if (!feedback) {
                feedback = document.createElement('div');
                feedback.className = 'dynamic-feedback small mt-1';
                container.parentElement.appendChild(feedback);
            }

            field.classList.toggle('is-invalid', !isValid);
            field.classList.toggle('is-valid', isValid);
            feedback.className = 'dynamic-feedback small mt-1 ' + (isValid ? 'text-success' : 'text-danger');
            feedback.textContent = message;
        }

        function renderPasswordChecklist(field) {
            var form = field.closest('.needs-constraint-validation');
            var checklist = form && form.querySelector('[data-password-checklist]');
            if (!checklist || field.value.length < 3) {
                if (checklist) {
                    checklist.replaceChildren();
                }
                return;
            }

            var rules = [
                ['Tối thiểu 8 ký tự', field.value.length >= 8],
                ['Có chữ hoa', /[A-ZÀ-Ỹ]/.test(field.value)],
                ['Có chữ thường', /[a-zà-ỹ]/.test(field.value)],
                ['Có chữ số', /\d/.test(field.value)],
                ['Có ký tự đặc biệt', /[^A-Za-zÀ-ỹ0-9]/.test(field.value)],
            ];

            checklist.replaceChildren(...rules.map(function (rule) {
                var item = document.createElement('div');
                item.className = rule[1] ? 'text-success' : 'text-danger';
                item.textContent = (rule[1] ? '✓ ' : '× ') + rule[0];
                return item;
            }));
        }

        function checkAvailability(field) {
            var form = field.closest('.needs-constraint-validation');
            if (!form || form.dataset.validationMode !== 'register' || !form.dataset.availabilityUrl) {
                return;
            }

            var value = field.value.trim();
            var fieldName = field.name;
            if (!['email', 'phone'].includes(fieldName) || value === '') {
                return;
            }

            clearTimeout(field.availabilityTimer);
            field.availabilityTimer = setTimeout(function () {
                var params = new URLSearchParams({ field: fieldName, value: value });
                fetch(form.dataset.availabilityUrl + '?' + params.toString(), {
                    headers: { Accept: 'application/json' },
                })
                    .then(function (response) {
                        if (!response.ok) {
                            throw new Error('Không thể kiểm tra dữ liệu lúc này.');
                        }
                        return response.json();
                    })
                    .then(function (result) {
                        if (field.value.trim() === value) {
                            setFieldFeedback(field, result.available, result.message);
                        }
                    })
                    .catch(function () {
                        // Server-side validation remains authoritative if availability check is unavailable.
                    });
            }, 400);
        }

        // Kiểm tra định dạng sau khi người dùng đã nhập đủ dữ liệu tối thiểu.
        function validateField(field, force = false) {
            var form = field.closest('.needs-constraint-validation');
            if (!form) return;

            var val = field.value;
            var name = field.name;
            var type = field.type;
            var isRequired = field.hasAttribute('required');

            var isValid = true;
            var errorMsg = '';
            var mode = form.dataset.validationMode || '';

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
                    if (val.trim().length >= 5 || force) {
                        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        if (!emailRegex.test(val.trim())) {
                            isValid = false;
                            errorMsg = 'Email không đúng định dạng (VD: builder@example.com).';
                        }
                    }
                }
                // Kiểm tra Số điện thoại
                else if (type === 'tel' || name === 'phone' || name === 'receiver_phone') {
                    var cleanPhone = val.replace(/[\s.-]/g, '');
                    if (cleanPhone.length >= 10 || force) {
                        var phoneRegex = /^(0|\+84)[0-9]{9,10}$/;
                        if (!phoneRegex.test(cleanPhone)) {
                            isValid = false;
                            errorMsg = 'Số điện thoại phải từ 10 số (VD: 0912345678).';
                        }
                    }
                }
                // Kiểm tra Mật khẩu
                else if (type === 'password' && (name === 'password' || name === 'new_password') && mode !== 'login') {
                    var passwordIsComplex = val.length >= 8
                        && /[A-ZÀ-Ỹ]/.test(val)
                        && /[a-zà-ỹ]/.test(val)
                        && /\d/.test(val)
                        && /[^A-Za-zÀ-ỹ0-9]/.test(val);
                    if (!passwordIsComplex) {
                        isValid = false;
                        errorMsg = 'Mật khẩu cần đủ 8 ký tự, chữ hoa, chữ thường, số và ký tự đặc biệt.';
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

            if (mode === 'login') {
                if (!isValid) {
                    setFieldFeedback(field, false, errorMsg);
                }

                return isValid;
            }

            if (isValid && val.trim() !== '') {
                setFieldFeedback(field, true, 'Hợp lệ');
            } else if (!isValid) {
                setFieldFeedback(field, false, errorMsg);
            }

            if (isValid && ['email', 'phone'].includes(name)) {
                checkAvailability(field);
            }
            return isValid;
        }

        document.addEventListener('input', function (e) {
            var field = e.target;
            if (!field.matches('.needs-constraint-validation input, .needs-constraint-validation textarea')) {
                return;
            }

            var form = field.closest('.needs-constraint-validation');
            var mode = form.dataset.validationMode || '';
            if (mode === 'login') {
                return;
            }
            if (mode === 'checkout' && field.name !== 'receiver_phone') {
                return;
            }
            if (field.name === 'password' && mode !== 'login') {
                renderPasswordChecklist(field);
                if (field.value.length >= 3) {
                    validateField(field);
                }
                var confirmation = form.querySelector('input[name="password_confirmation"]');
                if (confirmation && confirmation.value.length >= 3) {
                    validateField(confirmation);
                }
            } else if (field.name === 'password_confirmation' && field.value.length >= 3) {
                validateField(field);
            } else if (['email', 'phone'].includes(field.name) && field.value.trim().length >= 5) {
                validateField(field);
            } else if (field.dataset.touched === 'true') {
                validateField(field);
            }
        });

        document.addEventListener('blur', function (e) {
            var field = e.target;
            var form = field.closest('.needs-constraint-validation');
            if (field.matches('.needs-constraint-validation input, .needs-constraint-validation textarea')
                && (!form || (form.dataset.validationMode !== 'login'
                    && (form.dataset.validationMode !== 'checkout' || field.name === 'receiver_phone')))) {
                field.dataset.touched = 'true';
                validateField(field);
            }
        }, true);

        // Chặn gửi form nếu còn trường không hợp lệ
        document.addEventListener('submit', function (e) {
            var form = e.target.closest('.needs-constraint-validation');
            if (!form) return;

            var fields = form.querySelectorAll('input:not([type="hidden"]):not([type="submit"]):not([type="button"]), textarea');
            var formValid = true;

            fields.forEach(function (field) {
                var valid = validateField(field, true);
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

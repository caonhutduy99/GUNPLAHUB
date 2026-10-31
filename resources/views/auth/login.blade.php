@extends('layouts.app')

@section('title', 'Đăng nhập tài khoản - GunplaHub')

@section('content')
<div class="row justify-content-center py-4">
    <div class="col-md-5 col-lg-4">
        <div class="card shadow-sm border" style="border-radius: var(--gh-radius);">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <span class="gh-brand mb-2 d-inline-flex" style="font-size: 1.5rem;">
                        <span class="text-body">GUNPLA</span><span class="text-danger">HUB</span>
                    </span>
                    <h5 class="fw-bold mb-1">Chào mừng trở lại!</h5>
                    <p class="text-muted small mb-0">Đăng nhập tài khoản Builder để tiếp tục mua sắm</p>
                </div>

                @if(session('url.intended'))
                    <div class="alert alert-info py-2 small mb-3">
                        <x-icon name="info" class="me-1" />Vui lòng đăng nhập để tiếp tục.
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="needs-constraint-validation" data-validation-mode="login" novalidate>
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label small fw-bold text-muted text-uppercase" style="letter-spacing: 0.05em; font-size: 0.75rem;">Địa chỉ Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                               class="form-control @error('email') is-invalid @enderror" placeholder="name@example.com" required autofocus>
                        @error('email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-2">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label for="password" class="form-label small fw-bold text-muted text-uppercase mb-0" style="letter-spacing: 0.05em; font-size: 0.75rem;">Mật khẩu</label>
                            <a href="{{ route('password.request') }}" class="small text-danger text-decoration-none">Quên mật khẩu?</a>
                        </div>
                        <div class="input-group">
                            <input type="password" id="password" name="password"
                                   class="form-control @error('password') is-invalid @enderror" placeholder="••••••••" required>
                            <button class="btn btn-outline-secondary btn-toggle-password" type="button" aria-label="Hiện mật khẩu">
                                <x-icon name="eye" />
                            </button>
                        </div>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-check mb-4 mt-2">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label small text-muted" for="remember">Duy trì đăng nhập trên thiết bị này</label>
                    </div>

                    <button type="submit" class="btn btn-danger w-100 py-2 rounded-pill fw-bold shadow-sm" style="font-size: 0.95rem;">
                        Đăng nhập
                    </button>
                </form>

                <div class="text-center mt-4 pt-3 border-top small text-muted" style="border-color: var(--gh-border) !important;">
                    Chưa có tài khoản? <a href="{{ route('register') }}" class="text-danger fw-bold text-decoration-none ms-1">Đăng ký ngay</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

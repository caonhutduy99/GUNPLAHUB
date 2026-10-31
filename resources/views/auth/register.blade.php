@extends('layouts.app')

@section('title', 'Đăng ký tài khoản Builder - GunplaHub')

@section('content')
<div class="row justify-content-center py-4">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-sm border" style="border-radius: var(--gh-radius);">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <span class="gh-brand mb-2 d-inline-flex" style="font-size: 1.5rem;">
                        <span class="text-body">GUNPLA</span><span class="text-danger">HUB</span>
                    </span>
                    <h5 class="fw-bold mb-1">Tạo tài khoản thành viên</h5>
                    <p class="text-muted small mb-0">Nhận ưu đãi độc quyền, tích điểm và theo dõi đơn hàng tiện lợi</p>
                </div>

                <form method="POST" action="{{ route('register') }}" class="needs-constraint-validation" data-validation-mode="register" data-availability-url="{{ route('register.availability') }}" novalidate>
                    @csrf

                    <div class="mb-3">
                        <label for="full_name" class="form-label small fw-bold text-muted text-uppercase" style="letter-spacing: 0.05em; font-size: 0.75rem;">Họ và tên *</label>
                        <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}"
                               class="form-control @error('full_name') is-invalid @enderror" placeholder="Nguyễn Văn A" required autofocus>
                        @error('full_name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label small fw-bold text-muted text-uppercase" style="letter-spacing: 0.05em; font-size: 0.75rem;">Địa chỉ Email *</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                               class="form-control @error('email') is-invalid @enderror" placeholder="builder@example.com" required>
                        @error('email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label small fw-bold text-muted text-uppercase" style="letter-spacing: 0.05em; font-size: 0.75rem;">Số điện thoại *</label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
                               class="form-control @error('phone') is-invalid @enderror" placeholder="0912345678" required>
                        @error('phone')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-2 mb-4">
                        <div class="col-md-6">
                            <label for="password" class="form-label small fw-bold text-muted text-uppercase" style="letter-spacing: 0.05em; font-size: 0.75rem;">Mật khẩu *</label>
                            <div class="input-group">
                                <input type="password" id="password" name="password"
                                       class="form-control @error('password') is-invalid @enderror" placeholder="Tối thiểu 8 ký tự" required>
                                <button class="btn btn-outline-secondary btn-toggle-password" type="button" aria-label="Hiện mật khẩu">
                                    <x-icon name="eye" />
                                </button>
                            </div>
                            <div class="password-checklist small mt-2" data-password-checklist></div>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label small fw-bold text-muted text-uppercase" style="letter-spacing: 0.05em; font-size: 0.75rem;">Nhập lại mật khẩu *</label>
                            <div class="input-group">
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                       class="form-control" placeholder="Nhập lại mật khẩu" required>
                                <button class="btn btn-outline-secondary btn-toggle-password" type="button" aria-label="Hiện mật khẩu">
                                    <x-icon name="eye" />
                                </button>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-danger w-100 py-2 rounded-pill fw-bold shadow-sm" style="font-size: 0.95rem;">
                        Đăng ký tài khoản
                    </button>
                </form>

                <div class="text-center mt-4 pt-3 border-top small text-muted" style="border-color: var(--gh-border) !important;">
                    Đã có tài khoản GunplaHub? <a href="{{ route('login') }}" class="text-danger fw-bold text-decoration-none ms-1">Đăng nhập</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

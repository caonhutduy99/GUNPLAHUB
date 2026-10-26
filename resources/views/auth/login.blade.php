@extends('layouts.app')

@section('title', 'Đăng nhập - GunplaHub')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h4 class="mb-4 text-center">Đăng nhập</h4>

                @if(session('url.intended'))
                    <div class="alert alert-info py-2">Vui lòng đăng nhập để tiếp tục.</div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                               class="form-control @error('email') is-invalid @enderror" required autofocus>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Mật khẩu</label>
                        <input type="password" id="password" name="password"
                               class="form-control @error('password') is-invalid @enderror" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="text-end mb-3">
                        <a href="{{ route('password.request') }}" class="small">Quên mật khẩu?</a>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label" for="remember">Ghi nhớ đăng nhập</label>
                    </div>

                    <button type="submit" class="btn btn-dark w-100">Đăng nhập</button>
                </form>

                <p class="text-center mt-3 mb-0">
                    Chưa có tài khoản? <a href="{{ route('register') }}">Đăng ký</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

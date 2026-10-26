@extends('layouts.app')

@section('title', 'Quên mật khẩu - GunplaHub')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h4 class="mb-3 text-center">Quên mật khẩu</h4>
                <p class="text-muted small">Nhập email đã đăng ký. Chúng tôi sẽ gửi liên kết để bạn đặt lại mật khẩu.</p>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                               class="form-control @error('email') is-invalid @enderror" required autofocus>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-dark w-100">Gửi liên kết đặt lại mật khẩu</button>
                </form>

                <p class="text-center mt-3 mb-0">
                    <a href="{{ route('login') }}">Quay lại đăng nhập</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

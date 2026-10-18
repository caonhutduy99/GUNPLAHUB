@extends('layouts.app')

@section('title', 'Liên hệ - ' . config('shop.name'))

@section('content')
<div class="row g-4">
    {{-- Thông tin cửa hàng --}}
    <div class="col-lg-5">
        <div class="card shadow-sm h-100">
            <div class="card-body p-4">
                <h4 class="mb-4">{{ config('shop.name') }}</h4>

                <p class="mb-3">
                    <i class="bi bi-geo-alt-fill text-danger me-2"></i>
                    <strong>Địa chỉ:</strong>
                    {{ config('shop.address') ?: 'Đang cập nhật' }}
                </p>
                <p class="mb-3">
                    <i class="bi bi-telephone-fill text-danger me-2"></i>
                    <strong>Số điện thoại:</strong>
                    <a href="tel:{{ preg_replace('/\D/', '', config('shop.phone')) }}" class="text-reset">{{ config('shop.phone') }}</a>
                </p>
                <p class="mb-0">
                    <i class="bi bi-envelope-fill text-danger me-2"></i>
                    <strong>Email:</strong>
                    @if(config('shop.email'))
                        <a href="mailto:{{ config('shop.email') }}" class="text-reset">{{ config('shop.email') }}</a>
                    @else
                        Đang cập nhật
                    @endif
                </p>
            </div>
        </div>
    </div>

    {{-- Form liên hệ --}}
    <div class="col-lg-7">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h5 class="mb-4 text-uppercase">Liên hệ với chúng tôi</h5>

                <form method="POST" action="{{ route('contact.store') }}">
                    @csrf

                    <div class="mb-3">
                        <input type="text" name="name" value="{{ old('name', auth()->user()?->full_name) }}"
                               class="form-control @error('name') is-invalid @enderror" placeholder="Họ và tên*" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <input type="email" name="email" value="{{ old('email', auth()->user()?->email) }}"
                               class="form-control @error('email') is-invalid @enderror" placeholder="Email*" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <input type="text" name="phone" value="{{ old('phone', auth()->user()?->phone) }}"
                               class="form-control @error('phone') is-invalid @enderror" placeholder="Số điện thoại*" required>
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <textarea name="message" rows="5"
                                  class="form-control @error('message') is-invalid @enderror"
                                  placeholder="Nhập nội dung*" required>{{ old('message') }}</textarea>
                        @error('message')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-dark">Gửi liên hệ</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

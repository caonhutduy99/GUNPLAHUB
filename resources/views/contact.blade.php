@extends('layouts.app')

@section('title', 'Liên hệ & Hỗ trợ - ' . config('shop.name'))

@section('content')
<div class="mb-4">
    <h3 class="fw-bold mb-1" style="letter-spacing: -0.02em;">
        <i class="bi bi-chat-dots-fill text-danger me-2"></i>Liên hệ & Hỗ trợ khách hàng
    </h3>
    <p class="text-muted small mb-0">Chúng tôi sẵn sàng tư vấn và giải đáp mọi thắc mắc của Gunpla Builders.</p>
</div>

<div class="row g-4">
    {{-- Thông tin cửa hàng --}}
    <div class="col-lg-5">
        <div class="card shadow-sm border h-100" style="border-radius: var(--gh-radius);">
            <div class="card-body p-4 p-md-5">
                <div class="d-inline-flex align-items-center gap-2 mb-3">
                    <span class="badge bg-danger text-uppercase px-2" style="font-size: 0.65rem;">Hotline</span>
                    <span class="text-muted small fw-bold">HỖ TRỢ TRỰC TUYẾN 24/7</span>
                </div>
                <h4 class="fw-bold mb-3">{{ config('shop.name') }} Store</h4>
                <p class="text-muted small mb-4">
                    Hệ thống cung cấp mô hình Gunpla chính hãng Bandai Namco Nhật Bản, phụ kiện nâng cấp và dụng cụ lắp ráp chuyên nghiệp.
                </p>

                <div class="d-flex flex-column gap-3">
                    <div class="d-flex align-items-start gap-3 p-3 rounded-3 bg-body-tertiary border" style="border-color: var(--gh-border) !important;">
                        <i class="bi bi-geo-alt-fill text-danger fs-5 mt-1"></i>
                        <div>
                            <div class="small fw-bold text-uppercase text-muted" style="letter-spacing: 0.05em; font-size: 0.75rem;">Địa chỉ cửa hàng</div>
                            <div class="small fw-medium">{{ config('shop.address') ?: 'Hệ thống phân phối Gunpla chính hãng toàn quốc' }}</div>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-3 p-3 rounded-3 bg-body-tertiary border" style="border-color: var(--gh-border) !important;">
                        <i class="bi bi-telephone-fill text-danger fs-5 mt-1"></i>
                        <div>
                            <div class="small fw-bold text-uppercase text-muted" style="letter-spacing: 0.05em; font-size: 0.75rem;">Số điện thoại tư vấn</div>
                            <a href="tel:{{ preg_replace('/\D/', '', config('shop.phone')) }}" class="small fw-bold text-danger text-decoration-none fs-6">
                                {{ config('shop.phone') }}
                            </a>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-3 p-3 rounded-3 bg-body-tertiary border" style="border-color: var(--gh-border) !important;">
                        <i class="bi bi-envelope-fill text-danger fs-5 mt-1"></i>
                        <div>
                            <div class="small fw-bold text-uppercase text-muted" style="letter-spacing: 0.05em; font-size: 0.75rem;">Email tiếp nhận</div>
                            <div class="small fw-medium">{{ config('shop.email') ?: 'contact@gunplahub.vn' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Form liên hệ --}}
    <div class="col-lg-7">
        <div class="card shadow-sm border" style="border-radius: var(--gh-radius);">
            <div class="card-body p-4 p-md-5">
                <h5 class="fw-bold mb-2">Gửi tin nhắn cho chúng tôi</h5>
                <p class="text-muted small mb-4">Điền thông tin và nội dung yêu cầu, đội ngũ hỗ trợ sẽ phản hồi trong vòng 24 giờ.</p>

                <form method="POST" action="{{ route('contact.store') }}" class="needs-constraint-validation" novalidate>
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase" style="letter-spacing: 0.05em; font-size: 0.75rem;">Họ và tên *</label>
                            <input type="text" name="name" value="{{ old('name', auth()->user()?->full_name) }}"
                                   class="form-control @error('name') is-invalid @enderror" placeholder="Nguyễn Văn A" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase" style="letter-spacing: 0.05em; font-size: 0.75rem;">Số điện thoại *</label>
                            <input type="tel" name="phone" value="{{ old('phone', auth()->user()?->phone) }}"
                                   class="form-control @error('phone') is-invalid @enderror" placeholder="0912345678" required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase" style="letter-spacing: 0.05em; font-size: 0.75rem;">Địa chỉ Email *</label>
                            <input type="email" name="email" value="{{ old('email', auth()->user()?->email) }}"
                                   class="form-control @error('email') is-invalid @enderror" placeholder="builder@example.com" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase" style="letter-spacing: 0.05em; font-size: 0.75rem;">Nội dung tin nhắn / Yêu cầu *</label>
                            <textarea name="message" rows="4"
                                      class="form-control @error('message') is-invalid @enderror"
                                      placeholder="Hãy cho chúng tôi biết bạn cần hỗ trợ về sản phẩm, vận chuyển hay đơn hàng..." required>{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 pt-2">
                            <button type="submit" class="btn btn-danger btn-lg rounded-pill px-4 fw-semibold shadow-sm d-inline-flex align-items-center gap-2" style="font-size: 0.95rem;">
                                <i class="bi bi-send-fill"></i>
                                <span>Gửi tin nhắn liên hệ</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Thanh toán đơn hàng #' . $order->id . ' - GunplaHub')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card shadow-sm">
            <div class="card-body p-4 text-center">
                <h3 class="mb-2">Thanh toán đơn hàng #{{ $order->id }}</h3>
                <p class="text-muted">Tổng thanh toán: <strong class="text-danger">{{ \App\Support\Money::format($order->total_amount) }}</strong></p>

                @if($order->payment->payment_method === 'VIETQR')
                    <h5 class="mt-4">Quét mã VietQR</h5>
                    <img src="{{ $qrUrl }}" alt="Mã QR thanh toán" class="img-fluid border rounded p-2 bg-white" style="max-width: 320px;">
                    <p class="small text-muted mt-2 mb-1">Nội dung chuyển khoản: <strong>GUNPLA{{ $order->id }}</strong></p>
                    <p class="small text-muted">Tài khoản nhận: {{ $accountName }} · {{ $accountNumber }}</p>
                @else
                    <div class="alert alert-info mt-4">
                        Đây là màn hình mô phỏng cổng VNPAY sandbox. Không phát sinh giao dịch thật.
                    </div>
                @endif

                @if($order->payment->status === 'Chờ xử lý')
                    <form method="POST" action="{{ route('payments.simulate', $order) }}" class="mt-3">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            <x-icon name="circle-check" class="me-1" />Giả lập thanh toán thành công
                        </button>
                    </form>
                    <p class="small text-muted mt-2">Chỉ dùng được trong môi trường sandbox/demo.</p>
                @else
                    <div class="alert alert-success mt-3 mb-0">Đơn hàng đã được thanh toán.</div>
                @endif

                <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-secondary mt-3">Xem đơn hàng</a>
            </div>
        </div>
    </div>
</div>
@endsection

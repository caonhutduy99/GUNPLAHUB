@extends('layouts.app')

@section('title', 'Giỏ hàng - GunplaHub')

@section('content')
<h3 class="mb-3">Giỏ hàng của bạn</h3>

@foreach($warnings as $warning)
    <div class="alert alert-warning">{{ $warning }}</div>
@endforeach

@if($blacklisted)
    <div class="alert alert-danger">
        Tài khoản của bạn đang nằm trong danh sách hạn chế nên không thể đặt hàng. Vui lòng liên hệ cửa hàng để được hỗ trợ.
    </div>
@endif

@if($lines->isEmpty())
    <div class="card shadow-sm">
        <div class="card-body text-center py-5">
            <p class="text-muted mb-3">Giỏ hàng của bạn đang trống.</p>
            <a href="{{ route('products.index') }}" class="btn btn-dark">Tiếp tục mua sắm</a>
        </div>
    </div>
@else
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Sản phẩm</th>
                                <th class="text-end">Đơn giá</th>
                                <th style="width: 170px;" class="text-center">Số lượng</th>
                                <th class="text-end">Thành tiền</th>
                                <th style="width: 50px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lines as $line)
                                @php($product = $line->product)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            @if($product->image_url)
                                                <img src="{{ $product->image_url }}" alt="" style="width: 60px; height: 60px; object-fit: contain;" class="border rounded bg-white">
                                            @else
                                                <div class="bg-secondary-subtle rounded" style="width: 60px; height: 60px;"></div>
                                            @endif
                                            <div>
                                                <a href="{{ route('products.show', $product) }}" class="text-body fw-semibold text-decoration-none">{{ $product->name }}</a>
                                                <div class="small text-muted">{{ $product->sku }} · còn {{ $product->stock_quantity }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end">{{ \App\Support\Money::format($product->price) }}</td>
                                    <td class="text-center">
                                        <form method="POST" action="{{ route('cart.update', $product->sku) }}" class="d-flex gap-1 justify-content-center">
                                            @csrf
                                            @method('PATCH')
                                            <input type="number" name="quantity" value="{{ $line->quantity }}" min="1" max="{{ $product->stock_quantity }}"
                                                   class="form-control form-control-sm" style="width: 70px;">
                                            <button type="submit" class="btn btn-outline-secondary btn-sm">Cập nhật</button>
                                        </form>
                                    </td>
                                    <td class="text-end fw-semibold">{{ \App\Support\Money::format($product->price * $line->quantity) }}</td>
                                    <td class="text-end">
                                        <form method="POST" action="{{ route('cart.remove', $product->sku) }}"
                                              onsubmit="return confirm('Xóa sản phẩm này khỏi giỏ hàng?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <form method="POST" action="{{ route('cart.clear') }}" class="mt-3"
                  onsubmit="return confirm('Xóa toàn bộ giỏ hàng?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-secondary btn-sm">Xóa toàn bộ giỏ hàng</button>
                <a href="{{ route('products.index') }}" class="btn btn-outline-dark btn-sm">Tiếp tục mua sắm</a>
            </form>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3">Tóm tắt đơn hàng</h5>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Tạm tính</span>
                        <span>{{ \App\Support\Money::format($subtotal) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Phí vận chuyển</span>
                        @if($shippingFee > 0)
                            <span>{{ \App\Support\Money::format($shippingFee) }}</span>
                        @else
                            <span class="text-success">Miễn phí</span>
                        @endif
                    </div>

                    @if($shippingFee > 0)
                        <div class="small text-muted mb-2">
                            Mua thêm {{ \App\Support\Money::format($freeShippingFrom - $subtotal) }} để được miễn phí vận chuyển.
                        </div>
                    @endif

                    <hr>
                    <div class="d-flex justify-content-between fw-bold fs-5 mb-3">
                        <span>Tổng cộng</span>
                        <span class="text-danger">{{ \App\Support\Money::format($total) }}</span>
                    </div>

                    @if($blacklisted)
                        <button type="button" class="btn btn-dark w-100" disabled>Tiến hành đặt hàng</button>
                    @elseif(Route::has('checkout.create'))
                        <a href="{{ route('checkout.create') }}" class="btn btn-dark w-100">Tiến hành đặt hàng</a>
                    @else
                        <button type="button" class="btn btn-dark w-100" disabled>Tiến hành đặt hàng</button>
                    @endif

                    <div class="small text-muted mt-2">Mã giảm giá được áp dụng ở bước đặt hàng.</div>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection

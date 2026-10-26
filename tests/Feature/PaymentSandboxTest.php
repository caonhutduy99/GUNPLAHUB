<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentSandboxTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_complete_a_vietqr_sandbox_payment(): void
    {
        $user = User::factory()->create();
        $order = $this->createOrder($user, 'VIETQR');

        $this->actingAs($user)
            ->get(route('payments.show', $order))
            ->assertOk()
            ->assertSee('Quét mã VietQR');

        $this->actingAs($user)
            ->post(route('payments.simulate', $order))
            ->assertRedirect(route('orders.show', $order));

        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'status' => 'Thành công',
        ]);
    }

    private function createOrder(User $user, string $paymentMethod): Order
    {
        $category = Category::firstOrCreate(['name' => 'Gunpla HG'], ['sort_order' => 1]);
        $brand = Brand::firstOrCreate(['code' => 'BAN'], ['name' => 'Bandai', 'sort_order' => 1]);
        $product = Product::create([
            'sku' => 'BAN-HG-PAY-001',
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'name' => 'Sản phẩm thanh toán',
            'cost_price' => 100000,
            'price' => 120000,
            'price_type' => 'Giá bán lẻ',
            'stock_quantity' => 1,
            'grade' => 'HG',
            'scale' => '1/144',
            'series' => 'TEST',
            'status' => 'Còn hàng',
        ]);
        $order = Order::create([
            'user_id' => $user->id,
            'order_date' => now(),
            'total_amount' => 120000,
            'shipping_fee' => 0,
            'status' => Order::PENDING,
            'receiver_name' => $user->full_name,
            'receiver_phone' => $user->phone,
            'shipping_address' => 'Địa chỉ kiểm thử, quận 1, thành phố Hồ Chí Minh',
        ]);
        $order->details()->create([
            'product_sku' => $product->sku,
            'quantity' => 1,
            'unit_price' => $product->price,
        ]);
        Payment::create([
            'order_id' => $order->id,
            'payment_method' => $paymentMethod,
            'status' => 'Chờ xử lý',
        ]);
        CartItem::where('user_id', $user->id)->delete();

        return $order;
    }
}

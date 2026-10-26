<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WishlistFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_add_and_remove_a_product_from_wishlist(): void
    {
        $customer = User::factory()->create();
        $product = $this->createProduct();

        $this->actingAs($customer)
            ->post(route('wishlist.store', $product))
            ->assertRedirect();

        $this->assertDatabaseHas('wishlist_items', [
            'user_id' => $customer->id,
            'product_sku' => $product->sku,
        ]);

        $this->actingAs($customer)
            ->delete(route('wishlist.destroy', $product))
            ->assertRedirect();

        $this->assertDatabaseMissing('wishlist_items', [
            'user_id' => $customer->id,
            'product_sku' => $product->sku,
        ]);
    }

    public function test_guest_is_redirected_to_login_when_using_wishlist(): void
    {
        $product = $this->createProduct();

        $this->post(route('wishlist.store', $product))
            ->assertRedirect(route('login'));
    }

    public function test_admin_cannot_use_wishlist(): void
    {
        $admin = User::factory()->admin()->create();
        $product = $this->createProduct();

        $this->actingAs($admin)
            ->get(route('wishlist.index'))
            ->assertForbidden();
    }

    private function createProduct(): Product
    {
        $category = Category::firstOrCreate(['name' => 'Gunpla HG'], ['sort_order' => 1]);
        $brand = Brand::firstOrCreate(['code' => 'BAN'], ['name' => 'Bandai', 'sort_order' => 1]);

        return Product::firstOrCreate([
            'sku' => 'BAN-HG-TEST-001',
        ], [
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'name' => 'Sản phẩm kiểm thử',
            'cost_price' => 100000,
            'price' => 120000,
            'price_type' => 'Giá bán lẻ',
            'stock_quantity' => 1,
            'grade' => 'HG',
            'scale' => '1/144',
            'series' => 'TEST',
            'status' => 'Còn hàng',
        ]);
    }
}

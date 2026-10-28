<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use App\Support\Pricing;
use App\Support\ValidationMessages;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $items = $user->cartItems()->with('product.category')->get();

        $warnings = [];
        $lines = collect();

        // Đồng bộ giỏ hàng với dữ liệu thực tế của kho
        foreach ($items as $item) {
            $product = $item->product;

            if (! $product || $product->status !== 'Còn hàng' || $product->stock_quantity < 1) {
                $name = $product?->name ?? $item->product_sku;
                $warnings[] = "“{$name}” hiện không còn bán nên đã được gỡ khỏi giỏ hàng.";
                $item->delete();
                continue;
            }

            if ($item->quantity > $product->stock_quantity) {
                $item->update(['quantity' => $product->stock_quantity]);
                $warnings[] = "“{$product->name}” chỉ còn {$product->stock_quantity} sản phẩm, số lượng trong giỏ đã được điều chỉnh.";
            }

            $lines->push($item);
        }

        $subtotal = $lines->sum(fn ($line) => (float) $line->product->price * $line->quantity);
        $shippingFee = $lines->isEmpty() ? 0 : Pricing::shippingFee($subtotal);

        return view('cart.index', [
            'lines' => $lines,
            'warnings' => $warnings,
            'subtotal' => $subtotal,
            'shippingFee' => $shippingFee,
            'total' => $subtotal + $shippingFee,
            'freeShippingFrom' => Pricing::FREE_SHIPPING_FROM,
            'blacklisted' => (bool) $user->is_blacklisted,
        ]);
    }

    public function add(Request $request): RedirectResponse
    {
        $data = $request->validate(
            [
                'sku' => ['required', 'string', 'max:50'],
                'quantity' => ['required', 'integer', 'min:1', 'max:99'],
            ],
            ValidationMessages::common(),
            ['sku' => 'Mã sản phẩm', 'quantity' => 'Số lượng']
        );

        $product = Product::find($data['sku']);

        // Hết hàng / Ngừng kinh doanh thì không cho thêm vào giỏ
        if (! $product || $product->status !== 'Còn hàng' || $product->stock_quantity < 1) {
            return back()->with('error', 'Sản phẩm này hiện không thể mua.');
        }

        $item = CartItem::firstOrNew([
            'user_id' => $request->user()->id,
            'product_sku' => $product->sku,
        ]);

        $current = (int) $item->quantity;

        if ($current >= $product->stock_quantity) {
            return back()->with('error', "Giỏ hàng đã có tối đa {$product->stock_quantity} sản phẩm này (hết tồn kho).");
        }

        $newQuantity = $current + $data['quantity'];
        $limited = $newQuantity > $product->stock_quantity;
        $item->quantity = min($newQuantity, $product->stock_quantity);
        $item->save();

        if ($limited) {
            return $request->boolean('buy_now')
                ? redirect()->route('checkout.create')->with('warning', "Kho chỉ còn {$product->stock_quantity} sản phẩm, giỏ hàng đã được cập nhật theo số lượng tối đa.")
                : back()->with('warning', "Kho chỉ còn {$product->stock_quantity} sản phẩm, giỏ hàng đã được cập nhật theo số lượng tối đa.");
        }

        return $request->boolean('buy_now')
            ? redirect()->route('checkout.create')
            : back()->with('success', 'Đã thêm vào giỏ hàng.');
    }

    public function update(Request $request, string $sku): RedirectResponse
    {
        $data = $request->validate(
            ['quantity' => ['required', 'integer', 'min:1', 'max:99']],
            ValidationMessages::common(),
            ['quantity' => 'Số lượng']
        );

        $item = $request->user()->cartItems()->where('product_sku', $sku)->with('product')->firstOrFail();
        $product = $item->product;

        if ($product->status !== 'Còn hàng' || $product->stock_quantity < 1) {
            $item->delete();

            return redirect()->route('cart.index')->with('error', 'Sản phẩm này không còn bán nên đã được gỡ khỏi giỏ hàng.');
        }

        if ($data['quantity'] > $product->stock_quantity) {
            $item->update(['quantity' => $product->stock_quantity]);

            return redirect()->route('cart.index')
                ->with('warning', "Kho chỉ còn {$product->stock_quantity} sản phẩm, số lượng đã được điều chỉnh.");
        }

        $item->update(['quantity' => $data['quantity']]);

        return redirect()->route('cart.index')->with('success', 'Đã cập nhật giỏ hàng.');
    }

    public function remove(Request $request, string $sku): RedirectResponse
    {
        $request->user()->cartItems()->where('product_sku', $sku)->delete();

        return redirect()->route('cart.index')->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng.');
    }

    public function clear(Request $request): RedirectResponse
    {
        $request->user()->cartItems()->delete();

        return redirect()->route('cart.index')->with('success', 'Đã xóa toàn bộ giỏ hàng.');
    }
}

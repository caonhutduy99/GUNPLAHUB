<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Shipment;
use App\Models\User;
use App\Services\ShipmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShipmentSandboxTest extends TestCase
{
    use RefreshDatabase;

    public function test_shipment_sandbox_records_tracking_events(): void
    {
        $user = User::factory()->create();
        $order = Order::create([
            'user_id' => $user->id,
            'order_date' => now(),
            'total_amount' => 120000,
            'shipping_fee' => 0,
            'status' => Order::CONFIRMED,
            'receiver_name' => $user->full_name,
            'receiver_phone' => $user->phone,
            'shipping_address' => 'Địa chỉ kiểm thử, quận 1, thành phố Hồ Chí Minh',
        ]);

        $shipments = app(ShipmentService::class);
        $shipment = $shipments->create($order);
        $shipments->updateStatus($order, Order::AWAITING_PICKUP);
        $shipments->updateStatus($order, Order::SHIPPING);

        $shipment->refresh();

        $this->assertInstanceOf(Shipment::class, $shipment);
        $this->assertNotEmpty($shipment->ghn_order_code);
        $this->assertCount(3, $shipment->tracking_events);
        $this->assertSame(Order::SHIPPING, $shipment->status);
    }
}

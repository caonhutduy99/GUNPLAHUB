<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Shipment;
use Illuminate\Support\Str;

class ShipmentService
{
    public function create(Order $order): Shipment
    {
        $shipment = Shipment::firstOrCreate(
            ['order_id' => $order->id],
            [
                'ghn_order_code' => 'DEMO'.str()->upper(Str::random(10)),
                'carrier' => config('shipping.carrier', 'GHN Sandbox'),
                'tracking_url' => config('shipping.tracking_url'),
                'status' => Order::CONFIRMED,
                'tracking_events' => [],
            ]
        );

        return $this->recordStatus($shipment, $shipment->status);
    }

    public function updateStatus(Order $order, string $status): Shipment
    {
        $shipment = $order->shipment ?: $this->create($order);

        return $this->recordStatus($shipment, $status);
    }

    private function recordStatus(Shipment $shipment, string $status): Shipment
    {
        $events = $shipment->tracking_events ?? [];
        $lastEvent = end($events);

        if (($lastEvent['status'] ?? null) !== $status) {
            $events[] = [
                'status' => $status,
                'label' => $this->label($status),
                'occurred_at' => now()->toIso8601String(),
            ];
        }

        $shipment->update([
            'status' => $status,
            'tracking_events' => $events,
        ]);

        return $shipment->refresh();
    }

    private function label(string $status): string
    {
        return match ($status) {
            Order::CONFIRMED => 'Đơn vị vận chuyển đã tiếp nhận đơn',
            Order::AWAITING_PICKUP => 'Đang chờ lấy hàng tại cửa hàng',
            Order::PICKED_UP => 'Đơn vị vận chuyển đã lấy hàng',
            Order::SHIPPING => 'Đang vận chuyển đến người nhận',
            Order::DELIVERED => 'Giao hàng thành công',
            Order::DELIVERY_FAILED => 'Giao hàng không thành công',
            Order::RETURNING => 'Đang chuyển hoàn về cửa hàng',
            Order::RETURNED_TO_SHOP => 'Đã hoàn hàng về cửa hàng',
            Order::CANCELLED => 'Vận đơn đã bị hủy',
            default => $status,
        };
    }
}

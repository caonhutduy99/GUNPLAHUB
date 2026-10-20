<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'userCount' => User::where('role', 'customer')->count(),
            'productCount' => Product::count(),
            'orderCount' => Order::count(),
            'pendingCount' => Order::needsAction()->count(),
            'revenueToday' => (float) Order::revenue()
                ->deliveredBetween(Carbon::today(), Carbon::today()->endOfDay())->sum('total_amount'),
            'revenueMonth' => (float) Order::revenue()
                ->deliveredBetween(Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth())->sum('total_amount'),
        ]);
    }
}

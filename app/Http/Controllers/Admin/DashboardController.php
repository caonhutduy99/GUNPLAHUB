<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'userCount' => User::where('role', 'customer')->count(),
            'productCount' => Product::count(),
            'orderCount' => Order::count(),
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AdminController extends Controller
{
    public function dashboard()
{
    $today = Carbon::today();

    // Get orders for today
    $ordersToday = Order::whereDate('created_at', $today)->orderBy('created_at', 'desc')->get();

    // Get count of orders for today
    $ordersCountToday = $ordersToday->count();

    // Get total revenue for today
    $revenueToday = $ordersToday->sum('total_price');

    return view('dashboard', compact('ordersCountToday', 'revenueToday', 'ordersToday'));
}
}

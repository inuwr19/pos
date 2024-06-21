<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('orderProducts.product')->get();
        return view('order', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::with('orderProducts.product')->findOrFail($id);
        return view('orderproduct', compact('order'));
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();
        return redirect()->route('orders.index')->with('success', 'Order deleted successfully!');
    }
}

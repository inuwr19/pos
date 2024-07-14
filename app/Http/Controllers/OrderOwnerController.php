<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderOwnerController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('orderProducts.product');

        // Filter by date
        if ($request->has('date') && $request->date) {
            $query->whereDate('created_at', $request->date);
        }

        // Search by customer name
        if ($request->has('customer') && $request->customer) {
            $query->where('customer', 'like', '%' . $request->customer . '%');
        }

        $orders = $query->get();

        if ($request->ajax()) {
            return view('owner.order_table', compact('orders'))->render();
        }

        return view('owner.order', compact('orders'));
    }
    public function show($id)
    {
        $order = Order::with('orderProducts.product')->findOrFail($id);
        return view('owner.orderproduct', compact('order'));
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();
        return redirect()->route('ordersOwner.index')->with('success', 'Order deleted successfully!');
    }

    public function receipt(Order $order)
    {
        return view('receipt', compact('order'));
    }
}

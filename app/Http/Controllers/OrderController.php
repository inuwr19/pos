<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function orderIndex(Order $order)
    {
        $order->load('products');
        return view('orderproduct', compact('order'));
    }
    public function orderProductIndex(Order $orders)
    {
        $orders = Order::with('products')->get();
        return view('order', compact('orders'));
    }
    public function postorder(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        $order = Order::create([
            'user_id' => $request->user_id,
            'status' => 'pending',
        ]);

        $totalPrice = 0;

        foreach ($request->products as $product) {
            $productModel = Product::find($product['id']);
            $price = $productModel->price;
            $quantity = $product['quantity'];
            $totalPrice += $price * $quantity;

            $order->products()->attach($product['id'], [
                'quantity' => $quantity,
                'price' => $price,
            ]);
        }

        $order->update(['total_price' => $totalPrice]);

        // Fetch the order with products for displaying
        $order->load('products');

        return view('order', compact('order'));
    }
}

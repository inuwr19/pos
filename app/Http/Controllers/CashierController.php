<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CashierController extends Controller
{
    public function index()
    {
        // Get products grouped by category
        $products = Product::all()->groupBy('category');
        return view('cashier', compact('products'));
    }
    public function completeOrder(Request $request)
    {
        Log::info('Complete Order Request: ', $request->all());

        $request->validate([
            'customer' => 'required|string|max:255',
            'grand_total' => 'required|numeric',
            'payment_method' => 'required|string'
        ]);

        if ($request->payment_method === 'cash') {
            // Handle cash payment
            $order = $this->createOrder($request);
            if ($order) {
                return redirect()->route('cashier.index')->with('success', 'Order completed successfully!');
            } else {
                return redirect()->route('cashier.index')->with('error', 'Order failed to complete!');
            }
        } else if ($request->payment_method === 'non_cash') {
            // Handle non-cash payment via Midtrans
            $response = $this->initiateMidtransPayment($request);
            return response()->json($response);
        }
    }

    private function createOrder($request)
    {
        try {
            // Inisialisasi variabel total_price
            $totalPrice = 0;

            // Buat semua OrderProduct terlebih dahulu
            $orderProducts = [];
            foreach (json_decode($request->order_items, true) as $item) {
                $orderProduct = OrderProduct::create([
                    'code_order' => 'ORDER-' . time(), // Ganti ini sesuai kebutuhan
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ]);
                $orderProducts[] = $orderProduct;
                $totalPrice += $item['price'] * $item['quantity']; // Hitung total_price dari semua OrderProduct
                Log::info('Order Product Created: ', $orderProduct->toArray());
            }

            // Buat Order setelah semua OrderProduct berhasil dibuat
            $order = new Order();
            $order->order_product_id = $orderProducts[0]->id; // Contoh pengaturan order_product_id, sesuaikan dengan kebutuhan Anda
            $order->code_order = 'ORDER-' . time(); // Set nilai code_order secara otomatis
            $order->user_id = auth()->id();
            $order->customer = $request->customer;
            $order->total_price = $totalPrice;
            $order->status = 'completed';
            $order->save();

            Log::info('Order Created: ', $order->toArray());

            return $order;
        } catch (\Exception $e) {
            Log::error('Error Creating Order: ', ['message' => $e->getMessage()]);
            return false;
        }
    }
    private function initiateMidtransPayment($request)
    {
        $orderId = 'ORDER-' . time();
        $grossAmount = $request->grand_total;

        $midtransParams = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => $request->customer,
                'email' => 'customer@example.com', // Anda bisa mengubah ini sesuai kebutuhan
            ],
            'item_details' => array_map(function ($item) {
                return [
                    'id' => $item['id'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'name' => $item['name'],
                ];
            }, json_decode($request->order_items, true))
        ];

        // Panggil Midtrans API untuk memulai pembayaran
        \Midtrans\Config::$serverKey = 'SB-Mid-server-uafg-0TXWVx-oM2mQwTo5SGc';
        \Midtrans\Config::$isProduction = false;
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        $snapToken = \Midtrans\Snap::getSnapToken($midtransParams);

        return [
            'redirect_url' => "https://app.sandbox.midtrans.com/snap/v2/vtweb/{$snapToken}"
        ];
    }
}

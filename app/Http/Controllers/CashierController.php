<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;

class CashierController extends Controller
{
    public function index()
    {
        $products = Product::all()->groupBy('category');
        return view('cashier', compact('products'));
    }

    public function completeOrder(Request $request)
    {
        // Set konfigurasi Midtrans
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
        \Midtrans\Config::$is3ds = config('midtrans.is_3ds');

        Log::info('Complete Order Request: ', $request->all());

        $request->validate([
            'customer' => 'required|string|max:255',
            'grand_total' => 'required|numeric',
            'payment_method' => 'required|in:cash,non_cash' // Validasi payment_method
        ]);

        if ($request->payment_method === 'cash') {
            $order = $this->createOrder($request);
            if ($order) {
                $order->status = 'completed';
                $order->save();
                return response()->json(['order_id' => $order->id]);
            } else {
                return response()->json(['error' => 'Order creation failed!'], 500);
            }
        } else if ($request->payment_method === 'non_cash') {
            $order = $this->createOrder($request, false);
            if ($order) {
                $response = $this->initiateMidtransPayment($order, $request);
                $response['order_id'] = $order->id;
                return response()->json($response);
            } else {
                return response()->json(['error' => 'Order creation failed!'], 500);
            }
        }
    }

    private function createOrder($request, $completed = true)
    {
        try {
            $order = new Order();
            $order->code_order = 'ORDER-' . time();
            $order->user_id = auth()->id();
            $order->customer = $request->customer;
            $order->no_table = $request->table_number; // Tambahkan nomor meja ke order
            $order->total_price = $request->grand_total;
            $order->payment_method = $request->payment_method; // Tambahkan metode pembayaran ke order
            $order->status = $completed ? 'completed' : 'pending';
            $order->save();

            Log::info('Order Created: ', $order->toArray());

            foreach (json_decode($request->order_items, true) as $item) {
                $orderProduct = OrderProduct::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ]);
                Log::info('Order Product Created: ', $orderProduct->toArray());
            }

            return $order;
        } catch (\Exception $e) {
            Log::error('Error Creating Order: ', ['message' => $e->getMessage()]);
            return false;
        }
    }

    public function initiateMidtransPayment($order, $request)
    {
        $params = [
            'transaction_details' => [
                'order_id' => $order->code_order,  // Pastikan menggunakan code_order sebagai order_id
                'gross_amount' => $order->total_price,
            ],
            'customer_details' => [
                'first_name' => $request->customer,
                'email' => 'customer@example.com',
            ],
            'item_details' => $order->orderProducts->map(function($orderProduct) {
                return [
                    'id' => $orderProduct->product_id,
                    'price' => $orderProduct->price,
                    'quantity' => $orderProduct->quantity,
                    'name' => $orderProduct->product->name,
                ];
            })->toArray(),
        ];

        Log::info('Midtrans Payment Params: ', $params);

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);
            return ['snap_token' => $snapToken];
        } catch (\Exception $e) {
            Log::error('Midtrans Error: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    public function handleMidtransNotification(Request $request)
    {
        try {
            \Midtrans\Config::$serverKey = config('midtrans.server_key');
            \Midtrans\Config::$isProduction = config('midtrans.is_production');
            \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
            \Midtrans\Config::$is3ds = config('midtrans.is_3ds');

            if (empty(\Midtrans\Config::$serverKey)) {
                Log::error('Midtrans server key is not set.');
                return response()->json(['status' => 'error', 'message' => 'Server key is not set'], 500);
            }

            $notification = new \Midtrans\Notification();

            $orderId = $notification->order_id;
            $transactionStatus = $notification->transaction_status;

            Log::info('Midtrans Notification Received: ', (array)$notification);

            // Cari order berdasarkan code_order
            $order = Order::where('code_order', $orderId)->first();

            if ($order) {
                switch ($transactionStatus) {
                    case 'capture':
                    case 'settlement':
                        $order->status = 'completed';
                        $order->save();
                        return response()->json(['status' => 'success', 'receipt_url' => route('cashier.printReceipt', $order->id)], 200);
                    case 'pending':
                        $order->status = 'pending';
                        $order->save();
                        break;
                    default:
                        $order->status = 'failed';
                        $order->save();
                        break;
                }
            } else {
                Log::error('Order not found for order ID: ' . $orderId);
                return response()->json(['status' => 'failed', 'message' => 'Order not found'], 404);
            }
        } catch (\Exception $e) {
            Log::error('Exception in handleMidtransNotification: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }


    public function printReceipt($orderId)
    {
        $order = Order::with('orderProducts.product')->findOrFail($orderId);
        return view('receipt', compact('order'));
    }
}


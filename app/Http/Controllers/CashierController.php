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
            'payment_method' => 'required|string'
        ]);

        if ($request->payment_method === 'cash') {
            $order = $this->createOrder($request);
            if ($order) {
                $order->status = 'completed';
                $order->save();
                return redirect()->route('cashier.index')->with('success', 'Order completed successfully!');
            } else {
                return redirect()->route('cashier.index')->with('error', 'Order failed to complete!');
            }
        } else if ($request->payment_method === 'non_cash') {
            $order = $this->createOrder($request, false);
            if ($order) {
                $response = $this->initiateMidtransPayment($order, $request);
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
            $order->total_price = $request->grand_total;
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

    private function initiateMidtransPayment($order, $request)
    {
        $grossAmount = $order->total_price;

        $midtransParams = [
            'transaction_details' => [
                'order_id' => $order->code_order,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => $order->customer,
                'email' => 'customer@example.com',
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

        try {
            $snapToken = Snap::getSnapToken($midtransParams);
            return ['snap_token' => $snapToken];
        } catch (\Exception $e) {
            Log::error('Midtrans Error: ', ['message' => $e->getMessage()]);
            return ['error' => 'Midtrans payment initiation failed!'];
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

            $order = Order::where('code_order', $orderId)->first();

            if ($order) {
                switch ($transactionStatus) {
                    case 'capture':
                    case 'settlement':
                        $order->status = 'completed';
                        break;
                    case 'pending':
                        $order->status = 'pending';
                        break;
                    default:
                        $order->status = 'failed';
                        break;
                }
                $order->save();
                return response()->json(['status' => 'success'], 200);
            } else {
                Log::error('Order not found for order ID: ' . $orderId);
                return response()->json(['status' => 'failed', 'message' => 'Order not found'], 404);
            }
        } catch (\Exception $e) {
            Log::error('Exception in handleMidtransNotification: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

}

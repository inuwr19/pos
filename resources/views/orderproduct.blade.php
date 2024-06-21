@extends('layouts.app')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Order Details</h1>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Order #{{ $order->code_order }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Customer:</strong> {{ $order->customer }}</p>
                                <p><strong>Total Price:</strong> Rp{{ number_format($order->total_price, 0, ',', '.') }}</p>
                                <p><strong>Status:</strong> {{ $order->status }}</p>
                            </div>
                        </div>
                        <hr>
                        <h4>Order Products</h4>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->orderProducts as $orderProduct)
                                <tr>
                                    <td>{{ $orderProduct->product->name }}</td>
                                    <td>Rp{{ number_format($orderProduct->price, 0, ',', '.') }}</td>
                                    <td>{{ $orderProduct->quantity }}</td>
                                    <td>Rp{{ number_format($orderProduct->price * $orderProduct->quantity, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <a href="{{ route('orders.index') }}" class="btn btn-secondary">Back to Orders</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

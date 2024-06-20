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
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Order #{{ $order->id }}</h3>
                        </div>
                        <div class="card-body">
                            @if($order->products->isEmpty())
                                <div class="alert alert-info">
                                    No products found in this order.
                                </div>
                            @else
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Product Name</th>
                                            <th>Quantity</th>
                                            <th>Price</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($order->products as $product)
                                            <tr>
                                                <td>{{ $product->name }}</td>
                                                <td>{{ $product->pivot->quantity }}</td>
                                                <td>{{ $product->pivot->price }}</td>
                                                <td>{{ $product->pivot->quantity * $product->pivot->price }}</td>
                                            </tr>
                                        @endforeach
                                        <tr>
                                            <td colspan="3" class="text-right"><strong>Total</strong></td>
                                            <td><strong>{{ $order->total_price }}</strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            @endif
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('orderIndex') }}" class="btn btn-primary">Back to Orders</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

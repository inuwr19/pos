@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Orders</h1>
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
                            <h3 class="card-title">Order List</h3>
                        </div>
                        <div class="card-body">
                            @if($orders->isEmpty())
                                <div class="alert alert-info">
                                    No orders found.
                                </div>
                            @else
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>User ID</th>
                                            <th>Total Price</th>
                                            <th>Status</th>
                                            <th>Products</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($orders as $order)
                                            <tr>
                                                <td>{{ $order->id }}</td>
                                                <td>{{ $order->user_id }}</td>
                                                <td>{{ $order->total_price }}</td>
                                                <td>{{ $order->status }}</td>
                                                <td>
                                                    @foreach($order->products as $product)
                                                        <div>{{ $product->name }} (x{{ $product->pivot->quantity }})</div>
                                                    @endforeach
                                                </td>
                                                <td>
                                                    <a href="{{ route('orderIndex', $order->id) }}" class="btn btn-info">View</a>
                                                    <!-- Tambahkan tombol edit dan delete jika diperlukan -->
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

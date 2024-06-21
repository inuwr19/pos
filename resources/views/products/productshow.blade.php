@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Product Detail</h1>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <img class="img-fluid" src="{{ asset('storage/' . $product->img) }}" alt="{{ $product->name }}">
                                </div>
                                <div class="col-md-8">
                                    <h2>{{ $product->name }}</h2>
                                    <p><strong>Category:</strong> {{ $product->category }}</p>
                                    <p><strong>Price:</strong> Rp{{ number_format($product->price, 0, ',', '.') }}</p>
                                    <p><strong>Description:</strong></p>
                                    <p>{{ $product->description }}</p>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('products.index') }}" class="btn btn-secondary">Back to Product List</a>
                                <a href="{{ route('products.edit', ['product' => $product->id]) }}" class="btn btn-primary">Edit Product</a>
                                <form action="{{ route('products.destroy', ['product' => $product->id]) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this product?')">Delete Product</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

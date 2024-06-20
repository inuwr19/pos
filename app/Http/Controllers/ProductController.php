<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function productIndex()
    {
        $products = Product::all();
        return view('product', compact('products'));
    }
}

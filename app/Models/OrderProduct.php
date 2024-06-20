<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    use HasFactory;

    protected $fillable = ['code_order', 'product_id', 'quantity', 'price'];

    protected $table = 'order_products';

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_product_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

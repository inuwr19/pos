<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = ['code_order', 'order_product_id', 'user_id', 'customer', 'total_price', 'status'];

    public function orderProduct()
    {
        return $this->belongsTo(OrderProduct::class, 'order_product_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

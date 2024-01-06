<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'completed',
        'user_id'
    ];

    
    protected $casts = [
        'user_id'=> 'integer',
        'completed'=> 'boolean'
    ];

    
    public function cart () {
        return $this->belongsToMany(cart::class, 'cart_order', 'order_id', 'cart_id');
    }
}

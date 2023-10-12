<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'number',
        'paid'
    ];


    protected $cast = [
        'user_id'=> 'integer',
        'number'=> 'integer',
        'paid'=> 'boolean'
    ];


    
    public function user () {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }


    public function product () {
        return $this->belongsToMany(Product::class, 'cart_product', 'cart_id', 'product_id');
    }




}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;


    protected $fillable = [
        'name',
        'file_path',
        'price',
        'product_name',
        'discount_price',
        'discount_percentage',
        'instock'
    ];


    
    protected $casts = [
        'instock'=> 'boolean'
    ];


    
    public function category () {
        return $this->belongsToMany(category::class, 'category_product', 'product_id', 'category_id');
    }


    
    public function comment () {
        return $this->belongsToMany(comment::class, 'comment_product', 'product_id', 'comment_id');
    }

    
    public function cart () {
        return $this->belongsToMany(cart::class, 'cart_product', 'product_id', 'cart_id');
    }

    
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class comment extends Model
{
    use HasFactory;


    protected $fillable = [
        'comment',
        'rating',
        'user_id'
    ];

    protected $casts = [
        'rating'=> 'integer'
    ];


    public function product () {
        return $this->belongsToMany(Product::class, 'comment_product', 'comment_id', 'product_id');
    }

    
    public function user () {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }



}

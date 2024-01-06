<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tags extends Model
{
    use HasFactory;

    protected $fillable = [
        'tags'
    ];


    public function product () {
        return $this->belongsToMany(Product::class, 'product_tags', 'tag_id', 'product_id');
    }



}

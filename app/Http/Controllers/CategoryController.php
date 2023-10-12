<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\category;


class CategoryController extends Controller
{
    //
    
    public function store(Request $request) { 
        $fields = $request->validate([
            'category'=> 'required',
            'product_id'=> 'required|integer'
        ]);

        $admin = auth()->user()->admin;

        if($admin) {
            
            $product = Product::find($request->product_id);
            
    
            $category = category::create([
                'category'=> $request->category
            ]);
    
            $product->category()->attach($category);
            
            $response = [
                'product'=> $category,
                'message'=> "category added",
                'success' => true
            ];
    
            return response($response);        
        }
        else {
            $response = [
                'message'=> "you are not an admin",
                'success' => false
            ];
    
            return response($response);
        }
    }
}

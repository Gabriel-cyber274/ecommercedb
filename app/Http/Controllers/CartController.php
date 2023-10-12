<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\cart;

class CartController extends Controller
{
    //

    public function getCart (){
        $id = auth()->user()->id;
        $cart = cart::with('product')->where('user_id', $id)->get(); 

        
        $sortedL = collect($cart)->sortByDesc('id');
        $finalL  = [];
        foreach($sortedL->values()->all() as $data){
            $finalL[] = $data;
        }

        $response = [
            'cart'=> $finalL,
            'message'=> "cart retrieved",
            'success' => true
        ];

        return response($response);  
    }

    public function getAllPaid (){
        // $id = auth()->user()->id;
        $admin = auth()->user()->admin;
        $cart = cart::with('product')->where('paid', 1)->get(); 

        
        $sortedL = collect($cart)->sortByDesc('id');
        $finalL  = [];
        foreach($sortedL->values()->all() as $data){
            $finalL[] = $data;
        }

        if($admin) {
            $response = [
                'cart'=> $finalL,
                'message'=> "all purchase retrieved",
                'success' => true
            ];
    
            return response($response);  
        }else {
            $response = [
                'message'=> "you are not an admin",
                'success' => false
            ];
    
            return response($response);
        }

    }

    public function updateNumber (string $cartId, Request $request){
        $id = auth()->user()->id;

        $fields = $request->validate([
            'number'=> 'required',
        ]);

        
        // $cart = cart::with('product')->where('user_id', $id)->get(); 
        $cart = cart::with('product')->find($cartId)->first(); 

        $cart->update([
            'number'=> $request->number,
        ]);
        
        $response = [
            'cart'=> $cart,
            'message'=> "cart retrieved",
            'success' => true
        ];

        return response($response);  
    }

    public function updatePaid (string $cartId){
        $id = auth()->user()->id;

        
        // $cart = cart::with('product')->where('user_id', $id)->get(); 
        $cart = cart::with('product')->find($cartId)->first(); 

        $cart->update([
            'paid'=> true,
        ]);
        
        $response = [
            'cart'=> $cart,
            'message'=> "cart retrieved",
            'success' => true
        ];

        return response($response);  
    }
    
    public function store(Request $request) { 
        $fields = $request->validate([
            'number'=> 'required',
            'product_id'=> 'required|integer'
        ]);

        $id = auth()->user()->id;
        

        $product = Product::find($request->product_id);
        

        $cart = cart::create([
            'number'=> $request->number,
            'user_id'=> $id,
            'paid'=> false,
        ]);

        $product->cart()->attach($cart);
        
        $response = [
            'cart'=> $cart,
            'message'=> "cart added",
            'success' => true
        ];

        return response($response);        
    }
    
}

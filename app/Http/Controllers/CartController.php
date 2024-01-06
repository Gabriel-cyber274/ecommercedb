<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\cart;

class CartController extends Controller
{
    //

    public function getCart (){
        $id = auth()->user()->id;
        $cart = cart::with('product')->where('user_id', $id)->where('paid', 0)->get(); 

        
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

    public function getPendingOrder() {
        $id = auth()->user()->id;

        $order = Order::with('cart')->where('user_id', $id)->where('completed', 0)->get();


        $newOrder = [];


        foreach($order as $ord) {
            $cart = cart::with('product')->where('id', $ord->cart->first()->id)->get()->first();
            $newOrder[] = [
                'order_id'=> $ord->id,
                'cart'=> $cart
            ];
        }


        
        $response = [
            'order'=> $newOrder,
            'message'=> "pending order retrieved",
            'success' => true
        ];

        return response($response);  

    }

    public function completeOrder (string $orderId) {
        $order = Order::with('cart')->find($orderId)->first(); 

        if($order->completed == 0) {
            $order->update([
                'completed'=> true,
            ]);   

            
            $response = [
                'order'=> $order,
                'message'=> "order completed successfully",
                'success' => true
            ];
    
            return response($response);  

        }else {
            $response = [
                'message'=> "order already completed",
                'success' => false
            ];
    
            return response($response);  

        }


    }

    public function getCompletedOrder () {
        $id = auth()->user()->id;

        $order = Order::with('cart')->where('user_id', $id)->where('completed', 1)->get();


        $newOrder = [];


        foreach($order as $ord) {
            $cart = cart::with('product')->where('id', $ord->cart->first()->id)->get()->first();
            $newOrder[] = [
                'order_id'=> $ord->id,
                'cart'=> $cart
            ];
        }


        
        $response = [
            'order'=> $newOrder,
            'message'=> "pending order retrieved",
            'success' => true
        ];

        return response($response);  

    }

    public function updatePaid (string $cartId){
        $id = auth()->user()->id;

        
        $cart = cart::with('product')->where('id', $cartId)->get()->first(); 
        $cart2 = cart::with('product')->where('id', $cartId)->get();
        // $cart = cart::with('product')->find($cartId)->first(); 


        if($cart->paid == 0) {

            $cart->update([
                'paid'=> true,
            ]);
    
            foreach($cart2 as $pro) {
                $product = Product::with('user')->where('id', $pro->product->first()->id)->get()->first();
                
                $setOrder = Order::create([
                    'completed'=>false,
                    'user_id'=> $product->user->id,
                ]);
                $cart->order()->attach($setOrder);
            }
            
            
            $response = [
                'cart'=> $cart,
                'message'=> "item paid successfully",
                'success' => true
            ];
    
            return response($response);  
        }else {
            
            $response = [
                'message'=> "item already paid",
                'success' => false
            ];
    
            return response($response);  
            
        }
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

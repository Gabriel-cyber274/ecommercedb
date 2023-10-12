<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\comment;

class CommentController extends Controller
{
    //

    public function getComment (string $productid) {
        $product = Product::with('comment')->find($productid)->comment;
        $commentid = [];

        
        foreach($product as $comment) {
            $commentid[]= $comment->id;
        }

        $comment = comment::with('user')->find($commentid);
        
        $response = [
            'comment'=> $comment,
            'message'=> "comment retrieved",
            'success' => true
        ];

        return response($response);   
    }
    
    public function store(Request $request) { 
        $fields = $request->validate([
            'comment'=> 'required',
            'rating'=>'required',
            'product_id'=> 'required|integer'
        ]);

        $id = auth()->user()->id;


        $product = Product::find($request->product_id);
        

        $comment = comment::create([
            'user_id'=> $id,
            'comment'=> $request->comment,
            'rating'=> $request->rating
        ]);

        $product->comment()->attach($comment);
        
        $response = [
            'comment'=> $comment,
            'message'=> "comment added",
            'success' => true
        ];

        return response($response);        
    }


}

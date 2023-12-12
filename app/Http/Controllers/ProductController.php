<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use App\Models\category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getProducts(string $category)
    {
        //
        $product = Product::with(['category', 'comment'])->get();

        $cate_pro = [];

        foreach($product as $pro) {
            foreach(collect($pro->category)->where('category', $category)->values()->all() as $data){
                $cate_pro[] = Product::with(['category', 'comment'])->find($data->pivot->product_id);
            }
        }

        $response = [
            'product' => $cate_pro,
            'message'=> "product retrieved",
            'success' => true
        ];

        return response($response);
    }

    public function getUserInterestProducts () {
        $id = Auth()->user()->id;
        $user = User::where('id', $id)->with(['interest'])->get()->first();
        $product = Product::with(['category', 'comment'])->get();

        $newCat = [];

        foreach($user->interest as $category) {
            $category = category::with(['product'])->where('category', $category->category)->get();

            if(count($category) !== 0) {
                // $newCat[$category->category] = $category;
                $newCat[] = [
                    'category' => $category,
                ];
            }

        }
        
        $response = [
            // 'user'=> $user,
            // 'product'=> $product,
            'cat'=> $newCat,
            'message'=> "product retrieved",
            'success' => true
        ];
        return response($response);

    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $fields = $request->validate([
            'price' => 'required',
            'product_name' => 'required',
            'instock' => 'required|boolean',
            'file'=> 'required'
        ]);

        $admin = auth()->user()->admin;


        // 'name',
        // 'file_path',
        // 'price',
        // 'product_name',
        // 'discount_price',
        // 'discount_percentage',
        // 'instock'



        $file = new Product;
        
        if($request->file() && $admin == 1) { 
            $fileName = time().'_'.$request->file->getClientOriginalName();
            $filePath = $request->file('file')->storeAs('productimg', $fileName, 'public');
            $file->name = time().'_'.$request->file->getClientOriginalName();
            $file->price = $request->price;
            $file->product_name = $request->product_name;
            $file->instock = $request->instock;
            $file->file_path = '/storage/' . $filePath;
            $file->save();
            
            $response = [
                'id'=> $file->id,
                'path'=>$file->file_path,
                'product_name'=> $file->product_name,
                'price'=> $file->price,
                'instock'=> $file->instock,
                'message'=> "product uploaded",
                'success' => true
            ];
    
            return response($response);
        }
        else if($admin !== 1) {
            $response = [
                'message'=> "you are not an admin",
                'success' => false
            ];
    
            return response($response);
        }
        else {
            $response = [
                'message'=> "product upload failed",
                'success' => false
            ];
    
            return response($response);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use App\Models\cart;
use App\Models\category;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Validator;
use App\Models\tags;
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
                $sorted = $category->sortByDesc('id');
                $finalL  = [];
                foreach($sorted->values()->all() as $data){
                    $finalL[] = $data;
                }
                $newCat[] = [
                    'category' => $finalL,
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


    public function adminShowRoom() {
        $id = Auth()->user()->id;
        $user = User::where('id', $id)->with(['interest'])->get()->first();
        $product = Product::with(['category', 'comment'])->get();

        $newCat = [];

        foreach($user->interest as $category) {
            $category = category::with(['product'])->where('category', $category->category)->get();

            if(count($category) !== 0) {
                $sorted = $category->sortByDesc('id');
                $finalL  = [];
                foreach($sorted->values()->all() as $data){
                    $finalL[] = $data;
                }
                $newCat[] = [
                    'category' => collect($finalL)->map(function ($item) {
                        return $item->toArray(); // Convert each model to an array of attributes
                    })->slice(0, 6)->values(), 
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

    public function getAllUserProduct () {
        $id = Auth()->user()->id;
        $user = User::where('id', $id)->with(['interest'])->get()->first();
        $product = Product::with(['category', 'comment'])->get();

        $newCat = [];

        foreach($user->interest as $category) {
            $category = category::with(['product'])->where('category', $category->category)->get();

            if(count($category) !== 0) {
                foreach($category as $categ){
                    if(collect($categ->product)->first()->user_id == $id) {
                        $sorted = $category->sortByDesc('id');
                        $finalL  = [];
                        foreach($sorted->values()->all() as $data){
                            $finalL[] = $data;
                        }
                        $newCat[] = [
                            'category' => $finalL,
                        ];
                    }
                    
                    
                }
                // $newCat[$category->category] = $category;   
            }

        }

        $newCat = collect($newCat)->unique('category')->values()->all();
        
        $response = [
            // 'user'=> $user,
            // 'product'=> $product,
            'cat'=> $newCat,
            'message'=> "product retrieved",
            'success' => true
        ];
        return response($response);
    }


    public function addProductTags(Request $request) {
        $fields = $request->validate([
            'product_id' => 'required',
            'tags' => 'required',
        ]);

        $product = Product::find($request->product_id);

        $tag = tags::create([
            'tags' => $request->tags,
        ]);

        $product->tags()->attach($tag);

        
        $response = [
            'cart'=> $tag,
            'message'=> "tags added",
            'success' => true
        ];

        return response($response);   
                

    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        Log::info('Entering store method');

        // $fields = $request->validate([
        //     'price' => 'required',
        //     'product_name' => 'required',
        //     'instock' => 'required|boolean',
        //     'file'=> 'required',
        //     'description' => 'required',
        //     'featured'=> 'required',
        // ]);

        $fields = Validator::make($request->all(),[
            'price' => 'required',
            'product_name' => 'required',
            'instock' => 'required|boolean',
            'file'=> 'required',
            'description' => 'required',
            'featured'=> 'required',
        ]);

        if($fields->fails()) {
            $response = [
                'errors'=> $fields->errors(),
                'success' => false
            ];

            return response($response);
        }

        
        $admin = auth()->user()->admin;
        $id = auth()->user()->id;




        $file = new Product;
        
        if($request->hasFile('file') && $admin == 1) { 
            $fileName = time().'_'.$request->file->getClientOriginalName();
            $filePath = $request->file('file')->storeAs('productimg', $fileName, 'public');
            $file->name = time().'_'.$request->file->getClientOriginalName();
            $file->price = $request->price;
            $file->product_name = $request->product_name;
            $file->instock = $request->instock;
            $file->file_path = '/storage/' . $filePath;
            $file->description = $request->description;
            $file->size = $request->size;
            $file->color = $request->color;
            $file->pieces = $request->pieces;
            $file->carton = $request->carton;
            $file->featured = $request->featured;

            $file->user_id = $id;
            $file->save();

            
        // 'description',
        // 'size',
        // 'color',
        // 'pieces',
        // 'carton',
        // 'featured'
            
            $response = [
                'id'=> $file->id,
                'path'=>$file->file_path,
                'product_name'=> $file->product_name,
                'price'=> $file->price,
                'instock'=> $file->instock,
                'description' => $file->description,
                'size' => $request->size,
                'color' => $request->color,
                'pieces' => $request->pieces,
                'carton' => $request->carton,
                'featured' => $request->featured,
                'message'=> "product uploaded",
                'success' => true
            ];

            return response($response, 201);
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

        Log::info('File uploaded successfully');

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

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Response;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::post('/register', [AuthController::class, 'Register']);
Route::post('/login', [AuthController::class, 'Login']);
Route::put('/updateAdmin', [AuthController::class, 'updateAdmin']);
Route::put('/updateNotification', [AuthController::class, 'updateNotification']);
Route::put('/updateState', [AuthController::class, 'updateState']);
Route::post('/userInterest', [AuthController::class, 'userInterest']);

// checkEmail
// updateState
// 


Route::get('imgs/{filename}', function ($filename) {
    $path = public_path('storage/productimg/'. $filename);

    if(!file_exists($path)) {
        abort(404);
    }
    $file = file_get_contents($path);
    $type = mime_content_type($path);

    return Response::make($file, 200, [
        'Content-Type'=>$type,
        // 'Content-Disposition'=> 'inline, filename="'. $filename . '"',
    ]);

})->name('img.get');


Route::group(['middleware'=> ['auth:sanctum']], function () {
    Route::post('/createProduct', [ProductController::class, 'store']);
    Route::get('/getUserInterestProducts', [ProductController::class, 'getUserInterestProducts']);

    
    Route::post('/productCategory', [CategoryController::class, 'store']);
    Route::post('/productComment', [CommentController::class, 'store']);
    Route::post('/addCart', [CartController::class, 'store']);
    Route::get('/getComment/{productid}', [CommentController::class, 'getComment']);
    Route::get('/getCart', [CartController::class, 'getCart']);
    Route::get('/getAllPaid', [CartController::class, 'getAllPaid']);
    Route::get('/getProduct/{category}', [ProductController::class, 'getProducts']);
    Route::put('/updateNumber/{cartId}', [CartController::class, 'updateNumber']);
    Route::put('/updatePaid/{cartId}', [CartController::class, 'updatePaid']);

    
    // checkEmail

    // getAllPaid
    Route::post('/logout', [AuthController::class, 'Logout']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

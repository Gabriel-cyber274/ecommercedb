<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Interest;


class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */



    
     public function Login (Request $request) {
        $fields = Validator::make($request->all(), [
            'email'=> 'required|string',
            'password'=> 'required|string|min:8',
        ]);
        
        if($fields->fails()) {
            $response = [
                'errors'=> $fields->errors(),
                'success' => false
            ];

            return response($response);
        }

        $user = User::where('email', $request->email)->first();

        if(!$user || !Hash::check($request->password, $user->password)) {
            return response([
                'message' => 'incorrect credentials',
                'success' => false
            ]);
        }
        // else if(is_null($user->email_verified_at)) {
        //     return response([
        //         'message' => 'email not verified',
        //         'success' => false
        //     ], 401);
        // }

        $token = $user->createToken('myToken')->plainTextToken;
        
        
        $response = [
            'user'=> $user,
            'token'=> $token,
            'message'=> 'logged in',
            'success' => true
        ];

        return response($response, 200);


    }

    public function Register (Request $request) {
        $fields = Validator::make($request->all(),[
            'firstname'=> 'required|string',
            'surname'=> 'required|string',
            // 'state'=> 'required|string',
            // 'country'=> 'required|string',
            'number'=> 'required|string',
            // 'dob'=> 'required|string',
            'email'=> 'required|string|unique:users,email',
            'password'=> 'required|string|min:8|confirmed',
            // 'admin'=> 'required'
            // 'location'=>'nullable|string',
            // 'question'=> 'required|string',
            // 'answer'=> 'required|string',
        ]);

        if($fields->fails()) {
            $response = [
                'errors'=> $fields->errors(),
                'success' => false
            ];

            return response($response);
        }
        
        $user = User::create([
            'firstname'=> $request['firstname'],
            'surname'=> $request['surname'],
            'state'=> $request['state'],
            'lga'=> $request['lga'],
            'number'=> $request['number'],
            // 'dob'=> $request['dob'],
            'admin'=> $request['admin'],
            'email'=> $request['email'],
            'password' => bcrypt($request['password']),
        ]);
        
        $response = [
            'user'=> $user,
            'message'=> 'successful signup',
            'success' => true
        ];
        
        // Mail::to($user->email)->send(new MyCustomMail($data));

        return response($response);

    }

    public function updateAdmin (Request $request) {
        $fields = Validator::make($request->all(),[
            'email'=> 'required|string',
            'admin'=>'required'
        ]);

        $user = User::where('email', $request['email'])->get()->first();

        $user->update([
            'admin'=> $request['admin']
        ]);

        
        
        $response = [
            'user'=> $user,
            'message'=> 'userInfo retrieved',
            'success' => true
        ];

        return response($response);
    }


    
    public function updateNotification (Request $request) {
        $fields = Validator::make($request->all(),[
            'email'=> 'required|string',
            'notification'=>'required'
        ]);

        $user = User::where('email', $request['email'])->get()->first();

        $user->update([
            'notification'=> $request['notification']
        ]);

        
        
        $response = [
            'user'=> $user,
            'message'=> 'userInfo retrieved',
            'success' => true
        ];

        return response($response);
    }

    
    public function updateState (Request $request) {
        $fields = Validator::make($request->all(),[
            'email'=> 'required|string',
            'state'=>'required',
            'lga'=>'required',
        ]);

        $user = User::where('email', $request['email'])->get()->first();

        $user->update([
            'state'=> $request['state'],
            'lga'=> $request['lga'],
        ]);

        
        $response = [
            'user'=> $user,
            'message'=> 'userInfo retrieved',
            'success' => true
        ];

        return response($response);
    }


    public function userInterest (Request $request) {
        $fields = Validator::make($request->all(),[
            'email'=> 'required|string',
            'category'=> 'required',
        ]);

        $user = User::where('email', $request['email'])->get()->first();

        $interest = Interest::create([
            'user_id'=> $user->id,
            'category'=> $request['category'],
        ]);

        
        $token = $user->createToken('myToken')->plainTextToken;
        
        $allUserInterest = Interest::where('user_id', $user->id)->get();
        
        $response = [
            'user'=> $user,
            'token'=> $token,
            'interest'=> $allUserInterest,
            'message'=> 'user interest successful',
            'success' => true
        ];

        return response($response, 201);

    }

    public function Logout (Request $request) {
        $request->user()->tokens()->delete();

        return [
            'message'=> 'logged out',
            'success' => true
        ];
    }


}

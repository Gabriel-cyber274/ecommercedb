<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;


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

        return response($response, 201);


    }

    public function Register (Request $request) {
        $fields = Validator::make($request->all(),[
            'firstname'=> 'required|string',
            'surname'=> 'required|string',
            'state'=> 'required|string',
            'country'=> 'required|string',
            'number'=> 'required|string',
            'dob'=> 'required|string',
            'email'=> 'required|string|unique:users,email',
            'password'=> 'required|string|min:8|confirmed',
            'admin'=> 'required'
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
            'country'=> $request['country'],
            'number'=> $request['number'],
            'dob'=> $request['dob'],
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

    public function Logout (Request $request) {
        $request->user()->tokens()->delete();

        return [
            'message'=> 'logged out',
            'success' => true
        ];
    }


}

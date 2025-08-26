<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:sanctum')->only(['profile', 'logout']);
    }


    public function register(UserRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        $token =$user->createToken('api')->plainTextToken;
        return response()->json([
            'user'=>$user,
            'token'=>$token
        ]);
    }

    public function login(UserRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        if(!$user || !Hash::check($request->password, $user->password)){
            return response()->json(['message'=>'Invalid Credentials'], 422);
        }
        $token =$user->createToken('api')->plainTextToken;
        return response()->json([
            'user'=>$user,
            'token'=>$token
        ]);
    }

    public function profile(Request $request)
    {
        return response()->json([
            'user' => $request->user()
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }
}

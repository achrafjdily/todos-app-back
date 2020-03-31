<?php

namespace App\Http\Controllers;

use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AuthController extends Controller
{
    //
    public function signUp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fullname' => 'required|string|min:2|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:8|max:255',
            'username' => 'required|min:4|unique:users'
        ]);
        if ($validator->fails()) {
            return response()->json(["success"=>false,"errors"=>$validator->getMessageBag()], 200);
        } else {
            try {
                User::create([
                        'fullname' => $request->fullname,
                        'email' => $request->email,
                        'password' => Hash::make($request->password),
                        'username' => $request->username,
                        ]);
                return response()->json(["success"=>true,"message"=>"User created successfully"], 200);
            } catch (Exception $e) {
                return response()->json(["success"=>false,"errors"=>$e->getMessage()], 200);
            }
        }
    }
    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(["success"=>false, 'errors' => $validator->getMessageBag()], 200);
        } else {
            if(filter_var($request->username,FILTER_VALIDATE_EMAIL)){
                $credentials = request(['email', 'password']);
            }else{
                $credentials = request(['username', 'password']);
            }

            $credentials['deleted_at'] = null;
            if (!Auth::attempt($credentials)) {
                return response()->json([
                    "success" => false,
                    "type" => "Unauthorized",
                    'message' => 'Unauthorized'
                ], 201);
            }
            $user = $request->user();
            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->token;
            $token->expires_at = Carbon::now()->addWeeks(1);
            $token->save();
            return response()->json([
                "user" => $user,
                'success' => true,
                'access_token' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse(
                    $tokenResult->token->expires_at
                )->toDateTimeString()
            ]);
        }
    }

    public function verify(Request $request){
        if ($request->user()) {
            return response()->json(true);
        }
        return response()->json(false);

    }
    public function user(Request $request){
        return response()->json($request->user());
    }
    public function logout(Request $request){
        $request->user()->token()->revoke();
        return response()->json([
            "success" => true,
            'message' => 'Successfully logged out'
        ]);
    }
}

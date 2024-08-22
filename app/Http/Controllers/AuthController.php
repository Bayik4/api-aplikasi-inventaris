<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(UserRegisterRequest $request)
    {
        $data = $request->validated();

        $user = new User($data);
        $user->password = Hash::make($data['password']);
        $user->save();

        return response()->json([
            'status' => true,
            'data' => [],
            'message' => "Success register user"
        ], 201);
    }

    public function login(UserLoginRequest $request)
    {
        $data = $request->validated();

        $user = User::where('email', $data['email'])->first();
        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw new HttpResponseException(response()->json([
                'status' => false,
                'data' => [],
                'message' => 'Email or password wrong'
            ], 400));
        } else {
            $token = $user->createToken($data['email'])->plainTextToken;

            Auth::login($user);

            return response()->json([
                'status' => true,
                'data' => Auth::user(),
                'access_token' => $token,
                'token_type' => 'Bearer',
                'message' => 'Success login'
            ], 200);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'status' => true,
            'data' => [],
            'message' => 'You now logout'
        ], 200);
    }
}

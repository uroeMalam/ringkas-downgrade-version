<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function welcome()
    {
        return response()->json([
            'message' => 'Hi there :)'
        ]);
    }
    public function reject()
    {
        return response()->json([
            'status' => false,
            'message' => 'not able access this data, please do login'
        ]);
    }
    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('name', 'password'))) {
            return response()->json([
                'status' => false,
                'message' => 'Username atau password salah'
            ]);
        }

        try {
            $user = User::with(['level'])->firstOrFail(); // dont use get (collection or create token not able to generate)
    
            $token = $user->createToken('auth_token')->plainTextToken;
    
            return response()->json([
                'status' => true,
                'message' => 'Berhasil Login',
                'data' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th
            ]);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Berhasil logout',
        ]);
    }

    public function ubah_password(Request $request)
    {
        $request->user()->fill([
            'password' => Hash::make($request->password)
        ])->save();

        return response()->json([
            'status' => true,
            'message' => 'Berhasil Ubah Password',
        ]);
    }

    public function profile($idUser)
    {
        try {
            $user = User::with(['level'])
                    ->where('id',$idUser)
                    ->first();
            if ($user) {
                return response()->json([
                    'status' => true,
                    'message' => 'get data successfull',
                    'data' => $user,
                ]);
            }else {
                return response()->json([
                    'status' => true,
                    'message' => 'data empty',
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th
            ]);
        }
    }
}

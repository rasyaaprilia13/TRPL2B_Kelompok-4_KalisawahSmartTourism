<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8'
        ]);

        $key = Str::lower($request->email) . '|' . $request->ip();

        // Maksimal 3 percobaan login
        if (RateLimiter::tooManyAttempts($key, 3)) {

            $seconds = RateLimiter::availableIn($key);

            return response()->json([
                'message' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik."
            ], 429);
        }

        $user = User::where('email', $request->email)->first(); // Cari user berdasarkan email

        // Email tidak ditemukan atau password salah
        if (!$user || !Hash::check($request->password, $user->password)) {

            RateLimiter::hit($key, 300); // blok 5 menit

            return response()->json([
                'message' => 'Email atau password salah'
            ], 401);
        }

        // Login berhasil → reset counter
        RateLimiter::clear($key);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'user' => $user,
            'token' => $token
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            "message"=>"Logout berhasil"
        ]);
    }

    public function showRegister()
    {
        return view('auth.register');
    }

}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Referral;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'phone_number' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'gender' => 'required|string',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'referral_code' => 'nullable|exists:users,my_referral_code',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            do {
                $myReferralCode = strtoupper(Str::random(6));
            } while (User::where('my_referral_code', $myReferralCode)->exists());

            $user = User::create([
                'name' => $request->name,
                'username' => $request->username,
                'phone_number' => $request->phone_number,
                'country' => $request->country,
                'gender' => $request->gender,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'my_referral_code' => $myReferralCode,
                'referral_code' => $request->referral_code,
            ]);

            if ($request->filled('referral_code')) {
                $referrer = User::where('my_referral_code', $request->referral_code)->first();

                if ($referrer) {
                    Referral::create([
                        'referral_id' => $referrer->id,
                        'referee_id' => $user->id,
                        'level' => 1,
                    ]);
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'User registered successfully',
                'user' => $user
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong during registration',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Invalid credentials'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token,
        ], 200);
    }
}

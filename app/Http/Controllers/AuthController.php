<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request) {
        try {
            $request->validate([
                "name" => "required",
                "email" => "required|email|unique:users",
                "passwors" => "required|confirmed|min3",
            ]);

            User::create([
                "name" => $request->name,
                "email" => $request->email,
                "passwors" => Hash::make($request->password),
            ]);
            return response()->json(["message" => "new user registered"], 201);
        } catch(\Exception $e) {
            return response()->json(["message" => "registeration failed", 400]);
        }
    }
}

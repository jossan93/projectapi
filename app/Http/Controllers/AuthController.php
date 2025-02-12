<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request) {
        try {
            // Validate request data
            $request->validate([
                "name" => "required|string|max:255",
                "email" => "required|email|unique:users,email",
                "password" => "required|confirmed|min:3", // Password must match the confirmation field
            ]);

            // Create the new user
            User::create([
                "name" => $request->name,
                "email" => $request->email,
                "password" => Hash::make($request->password),
            ]);

            return response()->json(["message" => "New user registered successfully"], 201);
        } catch (\Exception $e) {
            // Return error message if registration fails
            return response()->json(["message" => "Registration failed: " . $e->getMessage()], 400);
        }
    }

    public function login(Request $request) {
        try {
            // Validate login credentials
            $request->validate([
                "email" => "required|email",
                "password" => "required",
            ]);

            // Find the user by email
            $user = User::where("email", $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                // Incorrect credentials
                throw ValidationException::withMessages([
                    "message" => ["Email or password is incorrect"]
                ]);
            }

            // Generate access token
            $token = $user->createToken("accessToken");

            return response()->json(["accessToken" => $token->plainTextToken], 200);
        } catch (\Exception $e) {
            // Handle exception and return error message
            return response()->json(["message" => "Login failed: " . $e->getMessage()], 400);
        }
    }

    public function logout(Request $request) {
        try {
            // Logout and delete all tokens
            $request->user()->tokens()->delete();
            return response()->json(["message" => "Logged out successfully"], 200);
        } catch (\Exception $e) {
            // Handle error during logout
            return response()->json(["message" => "Logout failed: " . $e->getMessage()], 400);
        }
    }
}


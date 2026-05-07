<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            "email" => "required|email",
            "password" => "required",
        ]);

        $user = User::where("email", $request->email)->first();

        if (!$user) {
            return response()->json(
                [
                    "message" => "User  not found",
                    "errors" => [
                        "email" => "This email is not registered",
                    ],
                ],
                404,
            );
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json(
                [
                    "message" => "Invalid Password",
                    "errors" => [
                        "password" => "Invalid password",
                    ],
                ],
                400,
            );
        }

        $token = $user->createToken(
            "auth-token",
            ["*"],
            Carbon::now()->addDays(30),
        )->plainTextToken;
        return response()->json(
            [
                "token" => $token,
                "data" => $user,
            ],
            200,
        );
    }

    public function authLogout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(["message", "Successfully Logout"], 200);
    }
}

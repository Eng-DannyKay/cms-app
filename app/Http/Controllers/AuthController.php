<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->authService->register($request->validated());

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
            'token' => $user->createToken('auth_token')->plainTextToken
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $user = $this->authService->login($request->validated());

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $user->createToken('auth_token')->plainTextToken
        ]);
    }

    public function logout(): JsonResponse
    {
        auth()->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }

    public function user(): JsonResponse
    {
        return response()->json(['user' => auth()->user()]);
    }
}

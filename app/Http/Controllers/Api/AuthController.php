<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    use ApiResponse;

    /**
     * Register a new user.
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role'     => 'required|in:seller,buyer',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => $request->password,
            'role'     => $request->role,
        ]);

        $token = JWTAuth::fromUser($user);

        return $this->successResponse([
            'user'  => $user,
            'token' => $token,
        ], 'User registered successfully', 201);
    }

    /**
     * Authenticate user and return a JWT token.
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }

        $credentials = $request->only('email', 'password');

        if (!$token = auth('api')->attempt($credentials)) {
            return $this->errorResponse('Invalid email or password', 401);
        }

        $user = auth('api')->user();

        return $this->successResponse([
            'user'  => $user,
            'token' => $token,
        ], 'Login successful');
    }

    /**
     * Log the user out (Invalidate the token).
     */
    public function logout(): JsonResponse
    {
        auth('api')->logout();

        return $this->successResponse(null, 'Successfully logged out');
    }

    /**
     * Refresh a token.
     */
    public function refresh(): JsonResponse
    {
        $token = auth('api')->refresh();
        $user  = auth('api')->user();

        return $this->successResponse([
            'user'  => $user,
            'token' => $token,
        ], 'Token refreshed');
    }

    /**
     * Get the authenticated user profile.
     */
    public function me(): JsonResponse
    {
        $user = auth('api')->user();
        $user->load('shop');

        return $this->successResponse($user, 'User profile');
    }
}

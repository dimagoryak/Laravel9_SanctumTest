<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    /**
     * Create new User
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function register(LoginRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->name ?? 'New User',
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Get bearer token or fail with 401 error
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::whereEmail($request->email)
            ->first();

        if (!$user || !$user->email_verified_at) {
            return response()->json([
                'message' => 'Invalid login details'
            ], 401);
        }

        if (!auth()->attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid login details'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Logout user
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        auth()->user()->tokens()->delete();

        return response()->json([
            'message' => 'Tokens Revoked'
        ]);
    }

    /**
     * refresh bearer token
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function refresh(Request $request): JsonResponse
    {
        $personalToken = PersonalAccessToken::findToken(
            $request->bearerToken()
        );

        if (!$personalToken) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $user = User::find($personalToken->tokenable_id);

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $user->tokens()->delete();

        return response()->json(
            [
                'token' => $user->createToken($user->name)->plainTextToken,
                'access_token' => $user->tokens()->first()->created_at ?? now()
            ]
        );
    }

    /**
     * @param Request $request
     * @return User|null
     */
    public function getUser(Request $request){
        return $request->user() ?? null;
    }
}

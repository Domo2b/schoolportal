<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = User::query()
            ->where('email', $data['identifier'])
            ->orWhereHas('student', fn ($query) => $query->where('matric_no', $data['identifier']))
            ->first();
        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages(['identifier' => ['The provided credentials are incorrect.']]);
        }
        $token = $user->createToken($data['device_name'] ?? 'student-portal')->plainTextToken;

        return response()->json(['user' => new UserResource($user->load('student')), 'token' => $token]);
    }

    public function me(Request $request): UserResource
    {
        return new UserResource($request->user()->load('student'));
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json(['message' => 'Logged out successfully.']);
    }
}

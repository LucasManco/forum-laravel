<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use \App\Models\User;

class UsersController extends Controller
{
    public function __construct(private \App\Models\User $user)
    {
    }

    public function signin(\App\Http\Requests\API\UsersRequest $request)
    {
        $user = $request->validated();
        $user['password_hash'] = Hash::make($user['password']);
        $user = $this->user->create($user);
        return response()->json($user, 201);
    }

    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }
        $token = $user->createToken('JWT');

        return response()->json(['token' => $token]);
    }

    public function logout(Request $request)
    {
        if (Auth::user()->currentAccessToken()->delete()) {
            return response()->json(['msg' => 'Logout realizado com sucesso.']);
        }
        return response()->json('Erro ao Processar a Informação', 401);
    }
}

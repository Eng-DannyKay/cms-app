<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Models\Client;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function register(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'client'
        ]);

        Client::create([
            'user_id' => $user->id,
            'company_name' => $data['company_name'],
            'slug' => \Illuminate\Support\Str::slug($data['company_name'])
        ]);

        return $user;
    }

    public function login(array $data): User
    {
        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return $user;
    }
}

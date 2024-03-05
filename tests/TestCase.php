<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function make_login(){
        $password = 'password';
        $user = User::factory(1)->createOne([
            'password_hash' => Hash::make($password),
        ]);

        $responseLogin = $this->post('/api/login', [
            'email' => $user->email,
            'password' => $password,
        ]);
        return $responseLogin->json('token')['plainTextToken'];
    }
}

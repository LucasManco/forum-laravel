<?php

namespace Tests\Feature\API;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Support\Facades\Hash;

class UsersControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */

    public function test_register_users_endpoint(): void
    {
        $user = User::factory(1)->makeOne()->toArray();
        $user['password'] = 'Password123';

        $response = $this->postJson('/api/signin', $user);

        $response->assertStatus(201);

        $response->assertJson(function (AssertableJson $assertableJson) use ($user) {

            $assertableJson->hasAll(['id', 'name', 'email', 'created_at', 'updated_at']);

            $assertableJson->whereAll([
                'name' => $user['name'],
                'email' => $user['email']
            ])->etc();
        });
    }

    public function test_login__and_logout_users_endpoint(): void
    {
        $password = 'password';
        $user = User::factory(1)->createOne([
            'password_hash' => Hash::make($password),
        ]);

        $responseLogin = $this->post('/api/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $responseLogin->assertStatus(200);
        $responseLogin->assertJson(function (AssertableJson $assertableJson) use ($user) {

            $assertableJson->hasAll(['token']);

            $assertableJson->where(
                'token.accessToken.name',
                'JWT'
            );
        });
        $token = $responseLogin->json('token')['plainTextToken'];
        $responseLogout = $this->withHeader('Authorization', 'Bearer ' . $token)->post('/api/logout');
        $responseLogout->assertStatus(200);
        $responseLogout->assertJson(function (AssertableJson $assertableJson) use ($user) {

            $assertableJson->hasAll(['msg']);

            $assertableJson->where(
                'msg',
                'Logout realizado com sucesso.'
            );
        });
    }
}

<?php

namespace Tests\Feature\API;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
    // public function test_get_users_endpoint(): void
    // {
    //     $users = User::factory(3)->create();

    //     $response = $this->getJson('/api/users');

    //     $response->assertStatus(200);

    //     $response->assertJsonCount(3);

    //     $response->assertJson(function (AssertableJson $assertableJson) use ($users) {

    //         $assertableJson->whereAllType([
    //             '0.id' => 'integer',
    //             '0.name' => 'string',
    //             '0.email' => 'string',
    //         ]);

    //         $assertableJson->hasAll(['0.id', '0.name', '0.email']);

    //         $user = $users->first();

    //         $assertableJson->whereAll([
    //             '0.id' => $user->id,
    //             '0.name' => $user->name,
    //             '0.email' => $user->email
    //         ]);
    //     });
    // }

    // public function test_show_users_endpoint(): void
    // {
    //     $user = User::factory(1)->createOne();

    //     $response = $this->getJson('/api/users/' . $user->id);

    //     $response->assertStatus(200);

    //     $response->assertJson(function (AssertableJson $assertableJson) use ($user) {
    //         $assertableJson->whereAllType([
    //             'id' => 'integer',
    //             'name' => 'string',
    //             'email' => 'string',
    //         ]);

    //         $assertableJson->hasAll(['id', 'name', 'email', 'created_at', 'updated_at']);

    //         $assertableJson->whereAll([
    //             'id' => $user->id,
    //             'name' => $user->name,
    //             'email' => $user->email
    //         ]);
    //     });
    // }

    public function test_register_users_endpoint(): void
    {
        $user = User::factory(1)->makeOne()->toArray();
        $user['password'] = 'Password123';
        // dd($user);

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

        // dd($user);

        $responseLogin = $this->post('/api/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $responseLogin->assertStatus(200);
        // $responseLogin->
        // dd($responseLogin->json());
        $responseLogin->assertJson(function (AssertableJson $assertableJson) use ($user) {
            
            $assertableJson->hasAll(['token']);
        
            $assertableJson->where(
                'token.accessToken.name', 'JWT');
        });
        $token = $responseLogin->json('token')['plainTextToken'];
        // dd($token);
        $responseLogout = $this->withHeader('Authorization', 'Bearer ' . $token)->post('/api/logout');
        $responseLogout->assertStatus(200);
        $responseLogout->assertJson(function (AssertableJson $assertableJson) use ($user) {
            
            $assertableJson->hasAll(['msg']);
        
            $assertableJson->where(
                'msg', 'Logout realizado com sucesso.');
        });
    }

    // public function test_post_users_should_validate_when_try_create_a_valid_question(): void
    // {
    //     $response = $this->postJson('/api/users', []);

    //     $response->assertStatus(422);

    //     $response->assertJson(function (AssertableJson $assertableJson) {

    //         $assertableJson->hasAll(['message', 'errors']);

    //         $assertableJson->where('errors.name.0', 'Este campo é obrigatório!')
    //                         ->where('errors.email.0', 'Este campo é obrigatório!');
    //     });
    // }

    // public function test_put_users_endpoint(): void
    // {
    //     $userDb = User::factory(1)->createOne();

    //     $user = [
    //         'name' => 'Atualizando Pergunta...',
    //         'email' => '1234567890'
    //     ];

    //     $response = $this->putJson('/api/users/' . $userDb->id, $user);

    //     $response->assertStatus(200);

    //     $response->assertJson(function (AssertableJson $assertableJson) use ($user) {
    //         $assertableJson->hasAll(['id', 'name', 'email', 'created_at', 'updated_at']);
    //         $assertableJson->whereAll([
    //             'name' => $user['name'],
    //             'email' => $user['email']
    //         ])->etc();
    //     });
    // }

    // public function test_patch_users_endpoint(): void
    // {
    //     $userDb = User::factory(1)->createOne();

    //     $user = [
    //         'name' => 'Atualizando Pergunta Patch...'
    //     ];

    //     $response = $this->patchJson('/api/users/' . $userDb->id, $user);

    //     $response->assertStatus(200);

    //     $response->assertJson(function (AssertableJson $assertableJson) use ($user) {
    //         $assertableJson->hasAll(['id', 'name', 'email', 'created_at', 'updated_at']);
    //         $assertableJson->where('name', $user['name']);
    //     });
    // }

    // public function test_delete_users_endpoint(): void
    // {
    //     $userDb = User::factory(1)->createOne();

    //     $response = $this->deleteJson('/api/users/' . $userDb->id);

    //     $response->assertStatus(204);
    // }
}

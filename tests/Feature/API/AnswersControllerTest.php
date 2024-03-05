<?php

namespace Tests\Feature\API;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Answer;
use App\Models\AnswerAttachment;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AnswersControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */

    private function make_login()
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
                'token.accessToken.name',
                'JWT'
            );
        });

        return $responseLogin->json('token')['plainTextToken'];
    }
    public function test_get_answers_endpoint(): void
    {
        $token = $this->make_login();

        $answers = Answer::factory(3)->create();
        AnswerAttachment::factory(1)->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/answers');

        $response->assertStatus(200);

        $response->assertJsonCount(3);

        $response->assertJson(function (AssertableJson $assertableJson) use ($answers) {

            $assertableJson->whereAllType([
                '0.id' => 'integer',
                '0.author_id' => 'integer',
                '0.question_id' => 'integer',
                '0.content' => 'string',
                '0.attachment' => 'array'
            ]);

            $assertableJson->hasAll(['0.id', '0.author_id', '0.question_id', '0.content']);

            $answer = $answers->first();

            $assertableJson->whereAll([
                '0.id' => $answer->id,
                '0.author_id' => $answer->author_id,
                '0.question_id' => $answer->question_id,
                '0.content' => $answer->content,
            ]);
        });
    }

    public function test_show_answers_endpoint(): void
    {
        $token = $this->make_login();

        $answer = Answer::factory(1)->createOne();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/answers/' . $answer->id);

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $assertableJson) use ($answer) {
            $assertableJson->whereAllType([
                'id' => 'integer',
                'author_id' => 'integer',
                'question_id' => 'integer',
                'content' => 'string',
            ]);

            $assertableJson->hasAll(['id', 'author_id', 'question_id', 'content', 'created_at', 'updated_at']);

            $assertableJson->whereAll([
                'id' => $answer->id,
                'author_id' => $answer->author_id,
                'question_id' => $answer->question_id,
                'content' => $answer->content
            ]);
        });
    }

    public function test_post_answers_endpoint(): void
    {
        $token = $this->make_login();

        $answer = Answer::factory(1)->makeOne()->toArray();
        
        Storage::fake('attachment');
 
        $file = UploadedFile::fake()->image('attachment.jpg');
 
        $answer['attachment'] = $file;
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/answers', $answer);

        $response->assertStatus(201);

        $response->assertJson(function (AssertableJson $assertableJson) use ($answer) {

            $assertableJson->hasAll(['id', 'author_id', 'question_id', 'content', 'created_at', 'updated_at']);

            $assertableJson->whereAll([
                'question_id' => $answer['question_id'],
                'content' => $answer['content'],
                'author_id' => $answer['author_id']
            ])->etc();
        });
    }

    public function test_post_answers_should_validate_when_try_create_a_valid_question(): void
    {
        $token = $this->make_login();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/answers', []);

        $response->assertStatus(422);

        $response->assertJson(function (AssertableJson $assertableJson) {

            $assertableJson->hasAll(['message', 'errors']);

            $assertableJson->where('errors.question_id.0', 'Este campo é obrigatório!')
                ->where('errors.content.0', 'Este campo é obrigatório!');
        });
    }

    public function test_put_answers_endpoint(): void
    {
        $token = $this->make_login();

        $answerDb = Answer::factory(1)->createOne();

        $answer = [
            'content' => '1234567890'
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/answers/' . $answerDb->id, $answer);

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $assertableJson) use ($answer) {
            $assertableJson->hasAll(['id', 'question_id', 'content', 'created_at', 'updated_at']);
            $assertableJson->whereAll([
                'content' => $answer['content']
            ])->etc();
        });
    }

    public function test_patch_answers_endpoint(): void
    {
        $token = $this->make_login();

        $answerDb = Answer::factory(1)->createOne();

        $answer = [
            'content' => 'Atualizando Pergunta Patch...'
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->patchJson('/api/answers/' . $answerDb->id, $answer);

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $assertableJson) use ($answer) {
            $assertableJson->hasAll(['id', 'question_id', 'content', 'author_id', 'created_at', 'updated_at']);
            $assertableJson->where('content', $answer['content']);
        });
    }

    public function test_only_author_may_update_answer():  void{
        User::factory(1)->createOne(); //cria um usuário
        $questionDb = Answer::factory(1)->createOne(); //cria a pergunta para o usuário 1
        $token = $this->make_login(); //loga no usuário 2

        $question = [
            'title' => 'Atualizando Pergunta Patch...'
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                            ->patchJson('/api/answers/' . $questionDb->id, $question);

        $response->assertStatus(403);
    }

    public function test_delete_answers_endpoint(): void
    {
        $token = $this->make_login();

        $answerDb = Answer::factory(1)->createOne();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/answers/' . $answerDb->id);

        $response->assertStatus(204);
    }
}

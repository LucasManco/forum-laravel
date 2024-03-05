<?php

namespace Tests\Feature\API;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Question;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class QuestionsControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    private function make_login(){
        $password = 'password';
        $user = User::factory(1)->createOne([
            'password_hash' => Hash::make($password),
        ]);

        // dd($user);

        $responseLogin = $this->post('/api/login', [
            'email' => $user->email,
            'password' => $password,
        ]);
        return $responseLogin->json('token')['plainTextToken'];
    }
    public function test_get_questions_endpoint(): void
    {
        $token = $this->make_login();
        $questions = Question::factory(3)->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                            ->getJson('/api/questions');

        $response->assertStatus(200);

        $response->assertJsonCount(3);

        $response->assertJson(function (AssertableJson $assertableJson) use ($questions) {

            $assertableJson->whereAllType([
                '0.id' => 'integer',
                '0.author_id' => 'integer',
                '0.title' => 'string',
                '0.content' => 'string',
                '0.slug' => 'string',
            ]);

            $assertableJson->hasAll(['0.id','0.author_id', '0.title', '0.content', '0.slug']);

            $question = $questions->first();

            $assertableJson->whereAll([
                '0.id' => $question->id,
                '0.author_id' => $question->author_id,
                '0.title' => $question->title,
                '0.content' => $question->content,
                '0.slug' => $question->slug
            ]);
        });
    }

    public function test_show_questions_endpoint(): void
    {
        $token = $this->make_login();
        $question = Question::factory(1)->createOne();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                            ->getJson('/api/questions/' . $question->id);

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $assertableJson) use ($question) {
            $assertableJson->whereAllType([
                'id' => 'integer',
                'author_id' => 'integer',
                'title' => 'string',
                'content' => 'string',
                'slug' => 'string',
            ]);

            $assertableJson->hasAll(['id', 'author_id', 'title', 'content', 'slug', 'created_at', 'updated_at']);

            $assertableJson->whereAll([
                'id' => $question->id,
                'author_id' => $question->author_id,
                'title' => $question->title,
                'content' => $question->content,
                'slug' => $question->slug
            ]);
        });
    }

    public function test_post_questions_endpoint(): void
    {
        $token = $this->make_login();

        $question = Question::factory(1)->makeOne()->toArray();

        Storage::fake('attachment');
 
        $file = UploadedFile::fake()->image('attachment.jpg');
 
        $question['attachment'] = $file;
             
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                            ->postJson('/api/questions', $question);

        $response->assertStatus(201);

        $response->assertJson(function (AssertableJson $assertableJson) use ($question) {

            $assertableJson->hasAll(['id', 'author_id', 'title', 'slug','content', 'created_at', 'updated_at']);

            $assertableJson->whereAll([
                'title' => $question['title'],
                'content' => $question['content'],
                'slug' => $question['slug']
            ])->etc();
        });
    }

    public function test_post_questions_should_validate_when_try_create_a_valid_question(): void
    {
        $token = $this->make_login();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                            ->postJson('/api/questions', []);

        $response->assertStatus(422);

        $response->assertJson(function (AssertableJson $assertableJson) {

            $assertableJson->hasAll(['message', 'errors']);

            $assertableJson->where('errors.title.0', 'Este campo é obrigatório!')
                            ->where('errors.content.0', 'Este campo é obrigatório!');
        });
    }

    public function test_put_questions_endpoint(): void
    {
        $token = $this->make_login();
        $questionDb = Question::factory(1)->createOne();
        
        $question = [
            'title' => 'Atualizando Pergunta...',
            'content' => '1234567890'
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                            ->putJson('/api/questions/' . $questionDb->id, $question);

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $assertableJson) use ($question) {
            $assertableJson->hasAll(['id', 'title', 'content', 'created_at', 'updated_at']);
            $assertableJson->whereAll([
                'title' => $question['title'],
                'content' => $question['content']
            ])->etc();
        });
    }

    public function test_patch_questions_endpoint(): void
    {
        $token = $this->make_login();
        $questionDb = Question::factory(1)->createOne();

        $question = [
            'title' => 'Atualizando Pergunta Patch...'
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                            ->patchJson('/api/questions/' . $questionDb->id, $question);

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $assertableJson) use ($question) {
            $assertableJson->hasAll(['id', 'title', 'content', 'author_id', 'slug', 'created_at', 'updated_at']);
            $assertableJson->where('title', $question['title']);
        });
    }

    public function test_only_author_may_update_question():  void{
        User::factory(1)->createOne(); //cria um usuário
        $questionDb = Question::factory(1)->createOne(); //cria a pergunta para o usuário 1
        $token = $this->make_login(); //loga no usuário 2

        $question = [
            'title' => 'Atualizando Pergunta Patch...'
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                            ->patchJson('/api/questions/' . $questionDb->id, $question);

        $response->assertStatus(403);
    }

    public function test_delete_questions_endpoint(): void
    {
        $token = $this->make_login();

        $questionDb = Question::factory(1)->createOne();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                            ->deleteJson('/api/questions/' . $questionDb->id);

        $response->assertStatus(204);
    }

    
}

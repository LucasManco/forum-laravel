<?php

namespace Tests\Feature\API;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Question;
use Illuminate\Testing\Fluent\AssertableJson;

class QuestionsControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_get_questions_endpoint(): void
    {
        $questions = Question::factory(3)->create();

        $response = $this->getJson('/api/questions');

        $response->assertStatus(200);

        $response->assertJsonCount(3);

        $response->assertJson(function (AssertableJson $assertableJson) use ($questions) {

            $assertableJson->whereAllType([
                '0.id' => 'integer',
                '0.title' => 'string',
                '0.isbn' => 'string',
            ]);

            $assertableJson->hasAll(['0.id', '0.title', '0.isbn']);

            $question = $questions->first();

            $assertableJson->whereAll([
                '0.id' => $question->id,
                '0.title' => $question->title,
                '0.isbn' => $question->isbn
            ]);
        });
    }

    public function test_show_questions_endpoint(): void
    {
        $question = Question::factory(1)->createOne();

        $response = $this->getJson('/api/questions/' . $question->id);

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $assertableJson) use ($question) {
            $assertableJson->whereAllType([
                'id' => 'integer',
                'title' => 'string',
                'isbn' => 'string',
            ]);

            $assertableJson->hasAll(['id', 'title', 'isbn', 'created_at', 'updated_at']);

            $assertableJson->whereAll([
                'id' => $question->id,
                'title' => $question->title,
                'isbn' => $question->isbn
            ]);
        });
    }

    public function test_post_questions_endpoint(): void
    {
        $question = Question::factory(1)->makeOne()->toArray();

        $response = $this->postJson('/api/questions', $question);

        $response->assertStatus(201);

        $response->assertJson(function (AssertableJson $assertableJson) use ($question) {

            $assertableJson->hasAll(['id', 'title', 'isbn', 'created_at', 'updated_at']);

            $assertableJson->whereAll([
                'title' => $question['title'],
                'isbn' => $question['isbn']
            ])->etc();
        });
    }

    public function test_post_questions_should_validate_when_try_create_a_valid_question(): void
    {
        $response = $this->postJson('/api/questions', []);

        $response->assertStatus(422);

        $response->assertJson(function (AssertableJson $assertableJson) {

            $assertableJson->hasAll(['message', 'errors']);

            $assertableJson->where('errors.title.0', 'Este campo é obrigatório!')
                            ->where('errors.isbn.0', 'Este campo é obrigatório!');
        });
    }

    public function test_put_questions_endpoint(): void
    {
        $questionDb = Question::factory(1)->createOne();

        $question = [
            'title' => 'Atualizando Pergunta...',
            'isbn' => '1234567890'
        ];

        $response = $this->putJson('/api/questions/' . $questionDb->id, $question);

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $assertableJson) use ($question) {
            $assertableJson->hasAll(['id', 'title', 'isbn', 'created_at', 'updated_at']);
            $assertableJson->whereAll([
                'title' => $question['title'],
                'isbn' => $question['isbn']
            ])->etc();
        });
    }

    public function test_patch_questions_endpoint(): void
    {
        $questionDb = Question::factory(1)->createOne();

        $question = [
            'title' => 'Atualizando Pergunta Patch...'
        ];

        $response = $this->patchJson('/api/questions/' . $questionDb->id, $question);

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $assertableJson) use ($question) {
            $assertableJson->hasAll(['id', 'title', 'isbn', 'created_at', 'updated_at']);
            $assertableJson->where('title', $question['title']);
        });
    }

    public function test_delete_questions_endpoint(): void
    {
        $questionDb = Question::factory(1)->createOne();

        $response = $this->deleteJson('/api/questions/' . $questionDb->id);

        $response->assertStatus(204);
    }
}

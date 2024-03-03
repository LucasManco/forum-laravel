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
        $question = Question::factory(1)->createOne();

        $response = $this->getJson('/api/questions/' . $question->id);

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
        $question = Question::factory(1)->makeOne()->toArray();

        $response = $this->postJson('/api/questions', $question);

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
        $response = $this->postJson('/api/questions', []);

        $response->assertStatus(422);

        $response->assertJson(function (AssertableJson $assertableJson) {

            $assertableJson->hasAll(['message', 'errors']);

            $assertableJson->where('errors.title.0', 'Este campo é obrigatório!')
                            ->where('errors.content.0', 'Este campo é obrigatório!');
        });
    }

    public function test_put_questions_endpoint(): void
    {
        $questionDb = Question::factory(1)->createOne();

        $question = [
            'title' => 'Atualizando Pergunta...',
            'content' => '1234567890'
        ];

        $response = $this->putJson('/api/questions/' . $questionDb->id, $question);

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
        $questionDb = Question::factory(1)->createOne();

        $question = [
            'title' => 'Atualizando Pergunta Patch...'
        ];

        $response = $this->patchJson('/api/questions/' . $questionDb->id, $question);

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $assertableJson) use ($question) {
            $assertableJson->hasAll(['id', 'title', 'content', 'author_id', 'slug', 'created_at', 'updated_at']);
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

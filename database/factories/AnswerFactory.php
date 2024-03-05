<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Question;



/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Question>
 */
class AnswerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::find(1);
        if(!$user){
            $user = User::factory(1)->createOne();
        }

        $question = Question::find(1);
        if(!$question){
            $question = Question::factory(1)->createOne();
        }
        return [
            'author_id'  => $user->id,
            'question_id' => $question->id,
            'content' => $this->faker->realText(180),
        ];
    }
}

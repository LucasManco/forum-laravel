<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Question>
 */
class QuestionFactory extends Factory
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
        return [
            'author_id'  => $user->id,
            'title' => $this->faker->sentence,
            'content' => $this->faker->realText(180),
            'slug' => $this->faker->sentence,       
        ];
    }
}

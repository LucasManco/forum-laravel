<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Answer;
use Illuminate\Http\UploadedFile;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AnswerAttachment>
 */
class AnswerAttachmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $answer = Answer::find(1);
        if(!$answer){
            $answer = Answer::factory(1)->createOne();
        }
        $file = UploadedFile::fake()->image('attachment.jpg');
        $file->store('attachment');

        return [
            'answer_id' => $answer->id,
            'content' => $file,
        ];
    }
}

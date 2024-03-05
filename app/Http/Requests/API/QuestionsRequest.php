<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Question;
use Illuminate\Support\Facades\Auth;

class QuestionsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $question_id = $this->route('question');
        if($question_id){
            $question = Question::find($question_id);
            // dd($question->author_id. ' ' . Auth::id());
            return $question->author_id == Auth::id() ;
        }
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return $this->route('question')?[]:
        [
            'title' => ['required','string'],
            'content' => ['required','string'],
            'slug' => ['required','string'],
            'attachment' => 'file'
        ];
    }

    public function messages()
    {
        return [
            'required' => 'Este campo é obrigatório!'
        ];
    }
}

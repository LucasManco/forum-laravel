<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Answer;
use Illuminate\Support\Facades\Auth;

class AnswersRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $answer_id = $this->route('answer');
        if($answer_id){
            $answer = Answer::find($answer_id);
            if($answer){
                return $answer->author_id == Auth::id() ;
            }
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
        return $this->route('answer')?[]:
        [
            'question_id' => 'required',
            'content' => 'required',
            'attachment' => ''
        ];
    }

    public function messages()
    {
        return [
            'required' => 'Este campo é obrigatório!'
        ];
    }
}

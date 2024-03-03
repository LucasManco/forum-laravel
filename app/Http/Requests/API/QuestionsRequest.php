<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class QuestionsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'author_id'  => 'required',
            'title' => 'required',
            'content' => 'required',
            'slug' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'required' => 'Este campo é obrigatório!'
        ];
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use \App\Models\QuestionAttachment;

class QuestionsController extends Controller
{
    public function __construct(private \App\Models\Question $question)
    {
    }
    public function index()
    {
        return response()->json($this->question->all());
    }

    public function show($id)
    {
        return response()->json($this->question->findOrFail($id));
    }

    public function store(\App\Http\Requests\API\QuestionsRequest $request)
    {
        $data = $request->validated();
        $data['author_id'] = Auth::id();
        
        $file = $request->file('attachment');
        $question = $this->question->create($data);
        if($file){
            $file->store('attachment');
        
            QuestionAttachment::create([
                'question_id' => $question->id,
                'content' => $file
            ]);
        }

        return response()->json($question, 201);
    }

    public function update($id, \App\Http\Requests\API\QuestionsRequest $request)
    {
        $question = $this->question->findOrFail($id);
        $question->update($request->all());
        return response()->json($question);
    }

    public function destroy($id)
    {
        $question = $this->question->findOrFail($id);
        return response()->json($question->delete(), 204);
    }
}

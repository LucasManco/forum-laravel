<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
        $question = $this->question->create($request->validated());
        return response()->json($question, 201);
    }

    public function update($id, Request $request)
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

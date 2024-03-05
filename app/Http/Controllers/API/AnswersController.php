<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use \App\Models\AnswerAttachment;

class AnswersController extends Controller
{
    public function __construct(private \App\Models\Answer $answer)
    {
    }
    public function index()
    {
        $answers = $this->answer->all();
        foreach($answers as $answer){
            $answer->attachment = $answer->getAttachments()->get();
        }
        
        return response()->json($answers);
    }

    public function show($id)
    {
        return response()->json($this->answer->findOrFail($id));
    }

    public function store(\App\Http\Requests\API\AnswersRequest $request)
    {
        $data = $request->validated();
        $data['author_id'] = Auth::id();
        $file = $request->file('attachment');
        $answer = $this->answer->create($data);   
        if($file){
            $file->store('attachment');
            AnswerAttachment::create([
                'answer_id' => $answer->id,
                'content' => $file
            ]);
        }
    
        return response()->json($answer, 201);
    }

    public function update($id, \App\Http\Requests\API\AnswersRequest $request)
    {
        $answer = $this->answer->findOrFail($id);
        $answer->update($request->all());
        return response()->json($answer);
    }

    public function destroy($id)
    {
        $answer = $this->answer->findOrFail($id);
        return response()->json($answer->delete(), 204);
    }
}

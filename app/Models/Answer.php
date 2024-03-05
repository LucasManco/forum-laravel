<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;

    protected $fillable = ['question_id', 'author_id','content'];

    public function getAttachments(){
        return $this->hasMany(AnswerAttachment::class, 'answer_id', 'id');
    }
}

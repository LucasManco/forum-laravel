<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnswerAttachment extends Model
{
    use HasFactory;

    protected $fillable = ['answer_id','content'];
}

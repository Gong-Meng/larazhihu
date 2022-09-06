<?php

namespace App\Http\Controllers;

use App\Models\Question;
use Illuminate\Http\Request;

class QuestionsController extends Controller
{
    //
    public function index()
    {

    }

    public function show($questionsId)
    {
        $question = Question::published()->findOrFail($questionsId);
        return view('questions.show', [
            'question' => $question,
            'answers' => $question->answers()->paginate(20)
        ]);
    }
}

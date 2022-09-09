<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Question;
use Illuminate\Http\Request;

class QuestionsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth')->except(['show', 'index']);

        $this->middleware('must-verify-email')->except(['index', 'show']);
    }

    //
    public function index()
    {

    }

    public function create(Question $question)
    {
        $categories = Category::all();
        return view('questions.create', [
            'question' => $question,
            'categories' => $categories
        ]);
    }

    public function store()
    {

        $this->validate(request(), [
            'title' => 'required',
            'content' => 'required',
            'category_id' => 'required|exists:categories,id'
        ]);

        $question = Question::create([
            'user_id' => auth()->id(),
            'category_id' => request('category_id'),
            'title' => request('title'),
            'content' => request('content')
        ]);
        return redirect('/drafts/' . $question->id)->with('flash', '保存成功！');
    }

    public function show($questionsId)
    {
        $question = Question::published()->findOrFail($questionsId);

        $answers = $question->answers()->paginate(20);

        array_map(function ($item) {
            return $this->appendVotedAttribute($item);
        }, $answers->items());

        return view('questions.show', [
            'question' => $question,
            'answers' => $answers
        ]);
    }
}

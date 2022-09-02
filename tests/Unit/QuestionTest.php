<?php

namespace Tests\Unit;


use App\Models\Question;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Answer;

class QuestionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @return void
     */
    public function a_question_has_many_answers()
    {
        $question = Question::factory()->create();
        create(Answer::class, ['question_id' => $question->id]);
        $this->assertInstanceOf(HasMany::class, $question->answers());
    }

    /**
     * @test
     * @return void
     */
    public function questions_with_published_at_date_are_published()
    {
        $publishedQuestion1 = Question::factory()->published()->create();
        $publishedQuestion2 = Question::factory()->published()->create();
        $unpublishedQuestion = Question::factory()->unpublished()->create();

        $publishedQuestions = Question::published()->get();

        $this->assertTrue($publishedQuestions->contains($publishedQuestion1));
        $this->assertTrue($publishedQuestions->contains($publishedQuestion2));
        $this->assertFalse($publishedQuestions->contains($unpublishedQuestion));
    }
}

<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Question;

class ViewQuestionsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function user_can_view_questions()
    {
        // 0 抛出异常
        $this->withoutExceptionHandling();

        // 1. 假设 /questions 路由存在
        // 2. 访问链接 /questions
        $test = $this->get('/questions');
        // 3. 正常返回200
        $test->assertStatus(200);

    }

    /**
     * @test
     * @return void
     */
    public function user_can_view_a_published_question()
    {
        $question = Question::factory()->create(['published_at' => Carbon::parse('-1 week')]);

        $this->get('/questions/' . $question->id)
            ->assertStatus(200)
            ->assertSee($question->title)
            ->assertSee($question->content);
    }

    /**
     * @test
     * @return void
     */
    public function user_cannot_view_unpublished_question()
    {
        $question = Question::factory()->create(['published_at' => null]);
        $this->withExceptionHandling()
            ->get('/questions/' . $question->id)
            ->assertStatus(404);
    }

    /**
     * @test
     * @return void
     */
    public function user_can_view_a_single_question()
    {
        // 1. 创建问题
        $question = Question::factory()->create();

        // 2. 访问链接
        $test = $this->get('/questions/' . $question->id);

        // 3. 那么应该看到问题的内容
        $test->assertStatus(200)
            ->assertSee($question->title)
            ->assertSee($question->content);
    }
}

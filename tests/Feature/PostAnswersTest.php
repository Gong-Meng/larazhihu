<?php

namespace Tests\Feature;

use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostAnswersTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @return void
     */
    public function user_can_post_an_answer_to_a_published_question()
    {
        // 假设已存在某个问题
        $question = Question::factory()->published()->create();
        $user = User::factory()->create();

        // 然后我们触发某个路由
        $response = $this->post("/questions/{$question->id}/answers", [
            'user_id' => $user->id,
            'content' => 'This is an answer.'
        ]);
        // 我们要看到预期结果
        $response->assertStatus(201);
        $answer = $question->answers()->where('user_id', $user->id)->first();
        $this->assertNotNull($answer);
        $this->assertEquals(1, $question->answers()->count());
    }

    /**
     * @test
     * @return void
     */
    public function can_not_post_an_answer_to_an_unpublished_question()
    {
        $question = Question::factory()->unpublished()->create();
        $user = User::factory()->create();
        $response = $this->withExceptionHandling()
            ->post("/questions/{$question->id}/answers", [
                'user_id' => $user->id,
                'content' => 'This is an answer.'
            ]);
        $response->assertStatus(404);
        $this->assertDatabaseMissing('answers', ['question_id' => $question->id]);
        $this->assertEquals(0, $question->answers()->count());
    }
}

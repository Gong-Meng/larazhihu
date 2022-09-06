<?php

namespace Tests\Feature\Answers;

use App\Models\Question;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostAnswersTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @return void
     */
    public function guests_may_not_post_an_answer()
    {
        // 第一种未认证测试逻辑

//        $this->withExceptionHandling();
//        $question = Question::factory()->published()->create();
//
//        $response = $this->post("/questions/{$question->id}/answers", [
//            'content' => 'This is an answer.'
//        ]);
//
//        $response->assertStatus(302)->assertRedirect('/login');

        // 第二种未认证测试逻辑
        $this->expectException(AuthenticationException::class);
        $question = Question::factory()->published()->create();

        $this->post("/questions/{$question->id}/answers", [
            'content' => 'This is an answer.'
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function signed_in_user_can_post_an_answer_to_a_published_question()
    {
        // 假设已存在某个问题
        $question = Question::factory()->published()->create();
        $this->signIn($user = create(User::class));

        // 然后我们触发某个路由
        $response = $this->post("/questions/{$question->id}/answers", [
            'content' => 'This is an answer.'
        ]);
        // 我们要看到预期结果
        $response->assertStatus(302);
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
        $this->signIn($user = create(User::class));
        $response = $this->withExceptionHandling()
            ->post("/questions/{$question->id}/answers", [
                'user_id' => $user->id,
                'content' => 'This is an answer.'
            ]);
        $response->assertStatus(404);
        $this->assertDatabaseMissing('answers', ['question_id' => $question->id]);
        $this->assertEquals(0, $question->answers()->count());
    }

    /**
     * @test
     * @return void
     */
    public function content_is_required_to_post_answers()
    {
        $this->withExceptionHandling();

        $question = Question::factory()->published()->create();
        $this->signIn($user = create(User::class));

        $response = $this->post("/questions/{$question->id}/answers", [
            'user_id' => $user->id,
            'content' => null
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('content');
    }
}

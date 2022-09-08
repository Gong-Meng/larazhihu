<?php

namespace Tests\Feature\Answers;

use App\Models\Answer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DownVotesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @return void
     */
    public function guest_can_not_vote_down()
    {
        $this->withExceptionHandling()
            ->post('/answers/1/down-votes')
            ->assertRedirect('/login');
    }

    /**
     * @test
     * @return void
     */
    public function authenticated_user_can_vote_down()
    {
        $this->signIn();
        $answer = create(Answer::class);
        $this->post("/answers/{$answer->id}/down-votes")
            ->assertStatus(201);
        $this->assertCount(
            1,
            $answer->refresh()->votes('vote_down')->get()
        );
    }

    /**
     * @test
     * @return void
     */
    public function an_authenticated_user_can_cancel_vote_down()
    {
        $this->signIn();
        $answer = create(Answer::class);
        $this->post("/answers/{$answer->id}/down-votes");
        $this->assertCount(
            1,
            $answer->refresh()->votes('vote_down')->get()
        );
        $this->delete("/answers/{$answer->id}/down-votes");
        $this->assertCount(
            0,
            $answer->refresh()->votes('vote_down')->get()
        );
    }

    /**
     * @test
     * @return void
     */
    public function can_vote_down_only_once()
    {
        $this->signIn();
        $answer = create(Answer::class);
        try {
            $this->post("/answers/{$answer->id}/down-votes");
            $this->post("/answers/{$answer->id}/down-votes");
        }catch (\Exception $e) {
            $this->fail('Can not vote down twice');
        }
        $this->assertCount(
            1,
            $answer->refresh()->votes('vote_down')->get()
        );
    }

    /**
     * @test
     * @return void
     */
    public function can_know_it_is_voted_down()
    {
        $this->signIn();
        $answer = create(Answer::class);
        $this->post("/answers/{$answer->id}/down-votes");
        $this->assertTrue($answer->refresh()->isVoteDown(auth()->user()));
    }
}

<?php

namespace Tests\Feature;

use App\Models\Answer;
use App\Models\User;

trait VoteUpContractTest
{
    /**
     * @test
     * @return void
     */
    public function guest_can_not_vote_up()
    {
        $this->withExceptionHandling()
            ->post('/answers/1/up-votes')
            ->assertRedirect('/login');
    }

    /**
     * @test
     * @return void
     */
    public function authenticated_user_can_vote_up()
    {
        $this->signIn();
        $answer = create(Answer::class);
        $this->post("/answers/{$answer->id}/up-votes")
            ->assertStatus(201);
        $this->assertCount(1, $answer->refresh()->votes('vote_up')->get());
    }

    /**
     * @test
     * @return void
     */
    public function can_vote_up_only_once()
    {
        $this->signIn();
        $answer = create(Answer::class);
        try {
            $this->post("/answers/{$answer->id}/up-votes");
            $this->post("/answers/{$answer->id}/up-votes");
        }catch (\Exception $e){
            $this->fail('Can not vote up twice.');
        }
        $this->assertCount(1, $answer->refresh()->votes('vote_up')->get());
    }

    /**
     * @test
     * @return void
     */
    public function an_authenticated_user_can_cancel_vote_up()
    {
        $this->signIn();
        $answer = create(Answer::class);
        $this->post("/answers/{$answer->id}/up-votes");
        $this->assertCount(1, $answer->refresh()->votes('vote_up')->get());
        $this->delete("/answers/{$answer->id}/up-votes");
        $this->assertCount(0, $answer->refresh()->votes('vote_up')->get());
    }

    /**
     * @test
     * @return void
     */
    public function can_know_it_is_voted_up()
    {
        $this->signIn();
        $answer = create(Answer::class);
        $this->post("/answers/{$answer->id}/up-votes");
        $this->assertTrue($answer->refresh()->isVotedUp(auth()->user()));
    }

    /**
     * @test
     * @return void
     */
    public function can_know_up_votes_count()
    {
        $answer = create(Answer::class);
        $this->signIn();
        $this->post("/answers/{$answer->id}/up-votes");
        $this->assertEquals(1, $answer->refresh()->upVotesCount);

        $this->signIn(create(User::class));
        $this->post("/answers/{$answer->id}/up-votes");

        $this->assertEquals(2, $answer->refresh()->upVotesCount);
    }

    abstract protected function getVoteUpUri($model = null);

    abstract protected function upVotes($model);

    abstract protected function getModel();
}

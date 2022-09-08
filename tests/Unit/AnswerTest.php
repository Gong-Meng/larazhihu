<?php

namespace Tests\Unit;

use App\Models\Answer;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnswerTest extends TestCase
{

    use RefreshDatabase;

    /**
     * @test
     * @return void
     */
    public function it_knows_if_it_is_the_best()
    {
        $answer = create(Answer::class);
        $this->assertFalse($answer->isBest());
        $answer->question->update(['best_answer_id' => $answer->id]);
        $this->assertTrue($answer->isBest());
    }

    /**
     * @test
     * @return void
     */
    public function an_answer_belongs_to_a_question()
    {
        $answer = create(Answer::class);
        $this->assertInstanceOf(BelongsTo::class, $answer->question());
    }

    /**
     * @test
     * @return void
     */
    public function an_answer_belongs_to_an_owner()
    {
       $answer = create(Answer::class);
       $this->assertInstanceOf(BelongsTo::class, $answer->owner());
       $this->assertInstanceOf(User::class, $answer->owner);
    }

    /**
     * @test
     * @return void
     */
    public function can_vote_up_an_answer()
    {
        $this->signIn();
        $answer = create(Answer::class);
        $this->assertDatabaseMissing('votes', [
            'user_id' => auth()->id(),
            'voted_id' => $answer->id,
            'voted_type' => get_class($answer),
            'type' => 'vote_up'
        ]);
        $answer->voteUp(auth()->user());
        $this->assertDatabaseHas('votes', [
           'user_id' => auth()->id(),
           'voted_id' => $answer->id,
           'voted_type' => get_class($answer),
           'type' => 'vote_up'
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function can_cancel_vote_up_an_answer()
    {
        $this->signIn();
        $answer = create(Answer::class);
        $answer->voteUp(auth()->user());
        $answer->cancelVoteUp(auth()->user());
        $this->assertDatabaseMissing('votes', [
            'user_id' => auth()->id(),
            'voted_id' => $answer->id,
            'voted_type' => get_class($answer)
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function can_know_it_is_voted_up()
    {
        $user = create(User::class);
        $answer = create(Answer::class);
        create(Vote::class, [
            'user_id' => $user->id,
            'voted_id' => $answer->id,
            'voted_type' => get_class($answer)
        ]);
        $this->assertTrue($answer->refresh()->isVoteUp($user));
    }

    /**
     * @test
     * @return void
     */
    public function can_vote_down_an_answer()
    {
        $this->signIn();
        $answer = create(Answer::class);
        $this->assertDatabaseMissing('votes', [
            'user_id' => auth()->id(),
            'voted_id' => $answer->id,
            'voted_type' => get_class($answer),
            'type' => 'vote_down',
        ]);
        $answer->voteDown(auth()->user());
        $this->assertDatabaseHas('votes', [
            'user_id' => auth()->id(),
            'voted_id' => $answer->id,
            'voted_type' => get_class($answer),
            'type' => 'vote_down',
        ]);
    }
}

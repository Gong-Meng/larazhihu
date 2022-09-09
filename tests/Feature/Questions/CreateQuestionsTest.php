<?php

namespace Tests\Feature\Questions;

use App\Models\Category;
use App\Models\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateQuestionsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @return void
     */
    public function guests_may_not_create_questions()
    {
        $this->withExceptionHandling();

        $this->post('/questions', [])
            ->assertRedirect('/login');
    }

    /**
     * @test
     * @return void
     */
    public function an_authenticated_user_can_create_new_questions()
    {
        $this->signIn();
        $question = make(Question::class);
        $this->assertCount(0, Question::all());
        $this->post('/questions', $question->toArray());
        $this->assertCount(1, Question::all());
    }

    /**
     * @test
     * @return void
     */
    public function title_is_required()
    {
        $this->signIn()->withExceptionHandling();
        $response = $this->post('/questions', ['title' => null]);
        $response->assertRedirect();
        $response->assertSessionHasErrors('title');
    }

    /**
     * @test
     * @return void
     */
    public function content_is_required()
    {
        $this->signIn()->withExceptionHandling();
        $question = make(Question::class)->toArray();
        unset($question['content']);
        $response = $this->post('/questions', $question);
        $response->assertRedirect();
        $response->assertSessionHasErrors('content');
    }

    /**
     * @test
     * @return void
     */
    public function category_id_is_required()
    {
        $this->signIn()->withExceptionHandling();

        $question = make(Question::class)->toArray();
        unset($question['category_id']);

        $response =$this->post('/questions', $question);

        $response->assertRedirect();
        $response->assertSessionHasErrors('category_id');
    }

    /**
     * @test
     * @return void
     */
    public function category_id_is_existed()
    {
        $this->signIn()->withExceptionHandling();
        create(Category::class, ['id' => 1]);
        $question = make(Question::class, ['category_id' => 999]);
        $response = $this->post('/questions', $question->toArray());
        $response->assertRedirect();
        $response->assertSessionHasErrors('category_id');
    }
}

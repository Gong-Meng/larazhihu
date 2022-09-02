<?php

namespace Database\Factories;

use App\Models\Question;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionFactory extends Factory
{
    protected $model = Question::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'user_id' => function () {
                return User::factory()->create()->id;
            },
            'title' => $this->faker->sentence,
            'content' => $this->faker->text,
        ];
    }

    public function published(): QuestionFactory
    {
        return $this->state(function () {
            return [
                'published_at' => Carbon::parse('-1 week')
            ];
        });
    }

    public function unpublished(): QuestionFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'published_at' => null
            ];
        });
    }
}

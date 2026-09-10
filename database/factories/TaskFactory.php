<?php

namespace Database\Factories;

use App\Enum\Priority;
use App\Enum\Status;
use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::query()->inRandomOrder()->value('id'),

            'category_id' => Category::query()
                ->where('is_active', true)
                ->inRandomOrder()
                ->value('id'),

            'title' => fake()->sentence(6),

            'description' => fake()->paragraph(),

            'priority' => fake()->randomElement(
                Priority::cases()
            )->value,

            'status' => fake()->randomElement(
                Status::cases()
            )->value,

            'due_date' => fake()->optional(0.8)->dateTimeBetween(
                'now',
                '+30 days'
            ),
        ];
    }
}

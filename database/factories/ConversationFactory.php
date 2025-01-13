<?php

namespace Database\Factories;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Conversation>
 */
class ConversationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Conversation::class;
    public function definition(): array
    {
        return [
         'last_message' => $this->faker->sentence(), // رسالة عشوائية
            'first_user' => $this->faker->numberBetween(1, 15), // رقم عشوائي بين 1 و 15
            'second_user' => $this->faker->numberBetween(1, 15), // رقم عشوائي بين 1 و 15
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}

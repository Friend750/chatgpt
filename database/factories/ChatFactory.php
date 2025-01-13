<?php

namespace Database\Factories;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Chat>
 */
class ChatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
          'conversation_id' => Conversation::factory(), // ينشئ محادثة جديدة بشكل تلقائي
            'sender_id' => $this->faker->numberBetween(1, 15), // ينشئ مستخدمًا كمرسل
            'receiver_id' => $this->faker->numberBetween(1, 15), // ينشئ مستخدمًا كمستلم
            'message' => $this->faker->sentence(), // رسالة عشوائية
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}

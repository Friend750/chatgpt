<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'user_name' => $this->faker->unique()->userName(),
            'password' => bcrypt('password'), // كلمة مرور مشفرة
            'user_image' => $this->faker->optional()->imageUrl(100, 100, 'people'), // صورة عشوائية
            'type' => $this->faker->randomElement(['admin', 'user', 'company']),
            'professional_summary' => $this->faker->optional()->paragraph(),
            'is_active' => $this->faker->boolean(90), // 90% من المستخدمين نشطين
            'is_connected' => $this->faker->boolean(50), // 50% من المستخدمين متصلين
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}

<?php

namespace Database\Factories;

use App\Models\User;
use App\Providers\AuthServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserRole>
 */
class UserRoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'created_by' => User::find(AuthServiceProvider::SUPER_ADMIN),
        ];
    }
}

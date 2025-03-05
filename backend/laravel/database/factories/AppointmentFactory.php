<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Client;

class AppointmentFactory extends Factory
{
    public function definition()
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id ?? User::factory(),
            'client_id' => Client::inRandomOrder()->first()->id ?? null,
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'date' => $this->faker->date(),
            'time' => $this->faker->time(),
            'duration' => $this->faker->numberBetween(30, 120),
            'location' => $this->faker->address(),
            'status' => $this->faker->randomElement(['geplant', 'erledigt', 'abgesagt']),
            'category' => $this->faker->word(),
        ];
    }
}


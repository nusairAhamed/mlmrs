<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class PatientFactory extends Factory
{
    public function definition(): array
    {
        return [
            'full_name'   => $this->faker->name(),
            'dob'         => $this->faker->dateTimeBetween('-70 years', '-1 years'),
            'gender'      => $this->faker->randomElement(['male', 'female', 'other']),
            'phone'       => $this->faker->numerify('07########'),
            'email'       => $this->faker->optional()->safeEmail(),
            'address'     => $this->faker->address(),
            'created_by'  => User::inRandomOrder()->value('id') ?? 1,
        ];
    }
}
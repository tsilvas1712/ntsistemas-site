<?php

namespace Database\Factories;

use App\Enums\ContactStatus;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Contact> */
class ContactFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->numerify('(51) 9####-####'),
            'company' => fake()->company(),
            'budget' => 'R$ 20k–50k',
            'stack' => 'Laravel, MySQL, AWS',
            'services' => ['legacy'],
            'message' => fake()->paragraph(),
            'status' => ContactStatus::Novo,
        ];
    }
}

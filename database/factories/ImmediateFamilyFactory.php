<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ImmediateFamily>
 */
class ImmediateFamilyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $relationships = ['spouse', 'child', 'parent', 'sibling', 'grandparent', 'grandchild'];
        
        return [
            'family_name' => $this->faker->name(),
            'relationship' => $this->faker->randomElement($relationships),
            'date_of_birth' => $this->faker->dateTimeBetween('-80 years', '-1 year')->format('Y-m-d'),
        ];
    }
}

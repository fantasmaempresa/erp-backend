<?php

namespace Database\Factories;

use App\Models\Concept;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConceptFactory extends Factory
{

    protected $model = Concept::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->jobTitle,
            'description' => $this->faker->text,
            'amount' => $this->faker->numberBetween(0, 100),
        ];
    }
}

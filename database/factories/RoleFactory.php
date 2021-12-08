<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @access  public
 *
 * @version 1.0
 */
class RoleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Role::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    #[ArrayShape(['name'        => "string",
                  'description' => "string",
    ])] public function definition(): array
    {
        return [
            'name'        => $this->faker->jobTitle,
            'description' => $this->faker->text,
        ];
    }
}

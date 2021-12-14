<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @access  public
 *
 * @version 1.0
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    #[ArrayShape([
        'name'     => "\Illuminate\Support\HigherOrderCollectionProxy|mixed",
        'email'    => "string",
        'phone'    => "string",
        'nickname' => "string",
        'rfc'      => "string",
        'user_id'  => "\Illuminate\Support\HigherOrderCollectionProxy|mixed",
    ])] public function definition(): array
    {

        return [
            'name'     => $this->faker->name,
            'email'    => $this->faker->companyEmail,
            'phone'    => Str::random(10),
            'nickname' => $this->faker->lastName,
            'rfc'      => Str::random(13),
        ];
    }
}

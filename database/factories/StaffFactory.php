<?php

namespace Database\Factories;

use App\Models\Staff;
use App\Models\WorkArea;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @access  public
 *
 * @version 1.0
 */
class StaffFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    #[ArrayShape([
        'name'         => "string",
        'email'        => "string",
        'phone'        => "string",
        'nickname'     => "string",
        'work_area_id' => "\Illuminate\Support\HigherOrderCollectionProxy|mixed",
    ])] public function definition(): array
    {
        $workArea = WorkArea::factory()->create();

        return [
            'name'         => $this->faker->name,
            'email'        => $this->faker->email,
            'phone'        => Str::random(10),
            'nickname'     => $this->faker->companySuffix,
            'work_area_id' => $workArea->id,
        ];
    }
}

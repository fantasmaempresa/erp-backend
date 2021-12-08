<?php

/*
 * CODE
 * Database Seeder
 */

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Role;
use App\Models\Staff;
use App\Models\User;
use App\Models\WorkArea;
use Illuminate\Database\Seeder;

/**
 * @access  public
 *
 * @version 1.0
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Role::factory()->count(5)->has(
            User::factory()->count(3)->has(Staff::factory())
        )->create();

        Role::factory()->count(5)->has(
            User::factory()->count(3)->has(Client::factory())
        )->create();
    }
}

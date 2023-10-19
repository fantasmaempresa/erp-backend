<?php

/*
 * CODE
 * Database Seeder
 */

namespace Database\Seeders;

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
//        Role::factory()->count(5)->has(
//            User::factory()->count(3)->has(Staff::factory())
//        )->create();
//
//        Role::factory()->count(5)->has(
//            User::factory()->count(3)->has(Client::factory())
//        )->create();

        $this->call(
            [
                RoleSeeder::class,
                UserSeeder::class,
                NotarySeeder::class,
                StatusQuoteSeeder::class,
                DocumentSeeder::class,
                OperationSedeer::class,
                TemplateShapeSeeder::class,
                PlaceSeeder::class,
                NCPISeeder::class,
                InversionUnitSeeder::class,
            ]
        );
    }
}

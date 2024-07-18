<?php

/*
 * CODE
 * Database Seeder
 */

namespace Database\Seeders;

use App\Models\CategoryOperation;
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

        $this->call(
            [
                // RoleSeeder::class,
                // UserSeeder::class,
                // NotarySeeder::class,
                // StatusQuoteSeeder::class,
                // DocumentSeeder::class,
                // OperationSedeer::class,
                // TemplateShapeSeeder::class,
                // PlaceSeeder::class,
                // StakeSeeder::class,
                // ProcedureSeeder::class,
                // GrantorSeeder::class,
                // Shape1Sedeer::class,
                // Shape2Sedder::class,
                // RegistrationDataSeeder::class, 
                // AppendantSeeder::class,
                // InversionUnitSeeder::class,
                // NCPISeeder::class,
                // RateSeeder::class,
                // TypeDisposalOperationSeeder::class,
                // EmployesNotarySeeder::class,
                // CategoryOperationSeeder::class,
                // UnitSeeder::class,
                // BookSeeder::class,
                FolioSeeder::class,
            ]
        );
    }
}

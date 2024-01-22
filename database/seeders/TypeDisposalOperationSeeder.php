<?php

/*
 * OPEN2CODE TypeDisposalOperation Seeder
 */

namespace Database\Seeders;

use App\Models\TypeDisposalOperation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * version
 */
class TypeDisposalOperationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        DB::table('type_disposal_operations')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');

        TypeDisposalOperation::create([
            'type' => 'SOLO TERRENO',
        ]);
        TypeDisposalOperation::create([
            'type' => 'TERRENO Y CONSTRUCCION ADQUIRIDOS EN DIFERENTES FECHAS',
        ]);
        TypeDisposalOperation::create([
            'type' => 'TERRENO Y CONSTRUCCION ADQUIRIDOS EN LAS MISMAS FECHAS',
        ]);
    }
}

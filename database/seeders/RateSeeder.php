<?php

/*
 * OPEN2CODE Rate Seeder
 */

namespace Database\Seeders;

use App\Imports\ImportCSV;
use App\Models\Rate;
use Box\Spout\Common\Exception\IOException;
use Box\Spout\Reader\Exception\ReaderNotOpenedException;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * version
 */
class RateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        DB::table('rates')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');

        $import = new ImportCSV(Storage::path('catalogs/rates.csv'), delimeter: '|');

        try {
            $catalogs = $import->readFile();

            foreach ($catalogs as $catalog) {
                Rate::create([
                    'year' => $catalog['EJERCICIO'],
                    'lower_limit' => $catalog['LIMITE INFERIOR'],
                    'upper_limit' => $catalog['LIMITE SUPERIOR'],
                    'fixed_fee' => $catalog['CUOTA FIJA'],
                    'surplus' => $catalog['EXEDENTE'],
                ]);
            }
        } catch (IOException|ReaderNotOpenedException $e) {
        }
    }
}

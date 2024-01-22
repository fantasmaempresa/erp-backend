<?php

/*
 * CODE
 * NCPI Seeder
 */

namespace Database\Seeders;

use App\Imports\ImportCSV;
use App\Models\NationalConsumerPriceIndex;
use Box\Spout\Common\Exception\IOException;
use Box\Spout\Reader\Exception\ReaderNotOpenedException;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * @access  public
 *
 * @version 1.0
 */
class NCPISeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        DB::table('national_consumer_price_indices')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');

        $import = new ImportCSV(Storage::path('catalogs/INPC.csv'), delimeter: '|');

        try {
            $catalogs = $import->readFile();

            foreach ($catalogs as $catalog) {
                $calendar = $catalog;
                $year = $calendar['YEAR'];
                unset($calendar['YEAR']);

                foreach ($calendar as $month => $value) {
                    NationalConsumerPriceIndex::create([
                        'year' => $year,
                        'month' => $month,
                        'value' => $value,
                    ]);
                }
            }
        } catch (IOException|ReaderNotOpenedException $e) {
        }
    }
}

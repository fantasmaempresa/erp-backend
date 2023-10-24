<?php

/*
 * OPEN 2 CODE
 * INVERSION UNIT SEEDER
 */
namespace Database\Seeders;

use App\Imports\ImportCSV;
use App\Models\InversionUnit;
use Box\Spout\Common\Exception\IOException;
use Box\Spout\Reader\Exception\ReaderNotOpenedException;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * @access  public
 *
 * @version 1.0
 */
class InversionUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        DB::table('inversion_units')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');

        $import = new ImportCSV(Storage::path('catalogs/UDIS.csv'), delimeter: '|');

        try {
            $catalogs = $import->readFile();

            foreach ($catalogs as $catalog) {
                $date = Carbon::createFromFormat('d/m/Y', $catalog['date'])->format('Y-m-d');
                $catalog['date'] = $date;
                InversionUnit::create($catalog);
            }
        } catch (IOException|ReaderNotOpenedException $e) {
        }
    }
}

<?php

namespace Database\Seeders;

use App\Imports\ImportCSV;
use App\Models\Unit;
use Box\Spout\Common\Exception\IOException;
use Box\Spout\Reader\Exception\ReaderNotOpenedException;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        DB::table('units')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');

        $import = new ImportCSV(Storage::path('catalogs/UMA.csv'), delimeter: '|');

        try {
            $catalogs = $import->readFile();
            
            foreach ($catalogs as $catalog) {
                Unit::create($catalog);
            }
        } catch (IOException|ReaderNotOpenedException $e) {
        }
    }
}

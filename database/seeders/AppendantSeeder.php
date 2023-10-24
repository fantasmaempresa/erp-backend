<?php

/*
 * OPEN2CODE
 * Appendant Seder
 */
namespace Database\Seeders;

use App\Imports\ImportCSV;
use App\Models\Appendant;
use App\Models\InversionUnit;
use Box\Spout\Common\Exception\IOException;
use Box\Spout\Reader\Exception\ReaderNotOpenedException;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * @version1
 */
class AppendantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        DB::table('appendants')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');

        $import = new ImportCSV(Storage::path('catalogs/Appendant9.csv'), delimeter: '|');

        try {
            $catalogs = $import->readFile();

            foreach ($catalogs as $catalog) {
                Appendant::create($catalog);
            }
        } catch (IOException|ReaderNotOpenedException $e) {
        }
    }
}

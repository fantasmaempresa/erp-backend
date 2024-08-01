<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class SqlFileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $folderPath = Storage::path('backup_server/SQL');
        $sqlFiles = File::files($folderPath);

        try {
            foreach ($sqlFiles as $sqlFile) {
                DB::unprepared(file_get_contents($sqlFile->getPathname()));
            }

            print_r("SQL Files Seeded");
            print_r("\n");
        } catch (Exception $e) {
            print_r("Error: " . $e->getMessage());
        }
    }
}

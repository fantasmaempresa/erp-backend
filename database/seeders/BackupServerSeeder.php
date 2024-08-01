<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class BackupServerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            SqlFileSeeder::class,
            UserBackupSeeder::class,
            GrantorBackupSeeder::class,
            ProcedureBackupSeeder::class,
            DocumentProcedureBackupSeeder::class
        ]);
    }
}

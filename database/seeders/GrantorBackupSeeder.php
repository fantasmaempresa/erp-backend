<?php

namespace Database\Seeders;

use App\Imports\ImportCSV;
use App\Models\Grantor;
use App\Models\Stake;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class GrantorBackupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $import = new ImportCSV(Storage::path('backup_server/stakes_backup.csv'), delimeter: '|');
        $stakes = $import->readFile();

        $import = new ImportCSV(Storage::path('backup_server/grantors_backup.csv'), delimeter: '|');
        $records = $import->readFile();
        $records = $records->where('father_last_name', '!=', "bk")->values();

        DB::beginTransaction();
        try {
            foreach ($records as $record) {
                $stakeServer = $stakes->where('id', trim($record['stake_id']))->first();
                $stakeDB = Stake::where('name', $stakeServer['name'])->first();
                $record['stake_id'] = $stakeDB->id;
                $record['email'] = (!empty($record['email'])) ? $record['email'] : null;
                $record['rfc'] = (!empty($record['rfc'])) ? $record['rfc'] : null;
                $record['curp'] = (!empty($record['curp'])) ? $record['curp'] : null;
                unset($record['id']);
                Grantor::create($record);
            }

            DB::commit();
            print_r('Grantor Backup Seeded');
            print_r("\n");
        } catch (Exception $e) {
            DB::rollBack();
            print_r('Grantor Backup Error: ' . $e->getMessage());
        }
    }
}

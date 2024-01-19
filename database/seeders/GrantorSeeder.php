<?php

namespace Database\Seeders;

use App\Imports\ImportCSV;
use App\Models\Grantor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class GrantorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        DB::table('grantors')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');

        $import = new ImportCSV(Storage::path('backup_sicon/otrogantes.csv'), delimeter: '|');

        try {
            $records = $import->readFile();

            foreach ($records as $records) {

                $grantor = new Grantor();

                $grantor->name = 'bk';
                $grantor->father_last_name = 'bk';
                $grantor->mother_last_name = 'bk';
                $grantor->rfc = 'bk';
                $grantor->curp = 'bk';
                $grantor->civil_status = 'bk';
                $grantor->municipality = 'bk';
                $grantor->colony = 'bk';
                $grantor->no_int = 'bk';
                $grantor->no_ext = 'bk';
                $grantor->no_locality = 'bk';
                $grantor->phone = 'bk';
                $grantor->locality = 'bk';
                $grantor->zipcode = 'bk';
                $grantor->place_of_birth = 'bk';
                $grantor->birthdate = 'bk';
                $grantor->occupation = 'bk';
                $grantor->type = 'bk';
                $grantor->stake_id = 'bk';
                $grantor->beneficiary = 'bk';

                $grantor->save();

            }
        } catch (IOException|ReaderNotOpenedException $e) {
            print_r('error al correr la migración ---> ', $e->getMessage());
        }
    }
}

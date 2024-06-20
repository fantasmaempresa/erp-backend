<?php

namespace Database\Seeders;

use App\Imports\ImportCSV;
use App\Models\Grantor;
use App\Models\Procedure;
use App\Models\Stake;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
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
        DB::table('grantor_procedure')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');

        $import = new ImportCSV(Storage::path('backup_sicom/otorgantes.csv'), delimeter: '|');
        $records = $import->readFile();
        $count = 0;
        foreach ($records as $record) {

            try {
                $stake = Stake::where('name', 'like', "%" . trim($record['Participacion']) . "%")->first();
                $procedure = Procedure::where('name', trim($record['Expediente']))->first();
                if (empty($procedure->id)) {
                    print_r("salto -------> " . trim($record['Participacion']) . ' ' . trim($record['Expediente']) . " \n");
                    continue;
                }

                $grantor = new Grantor();

                $grantor->name = trim($record['Otorgante']);
                $grantor->father_last_name = 'bk';
                $grantor->mother_last_name = 'bk';
                $grantor->rfc = 'bk' . $count;
                $grantor->curp = 'bk' . $count;
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
                $grantor->birthdate = Carbon::today();
                $grantor->occupation = 'bk';
                $grantor->type = 'bk';
                $grantor->stake_id = empty($stake) ? 1 : $stake->id;
                $grantor->beneficiary = trim($record['Beneficiario']) == "FALSO" ? false : true;
                $grantor->save();
                $grantor->procedures()->attach($procedure->id);

                $count++;

            } catch (IOException|ReaderNotOpenedException $e) {
                print_r('error al correr la migración ---> ', $e->getMessage());
            } catch (QueryException $exception) {
                print_r('error no ingresado --> ' . $exception->getMessage());
                exit();
            }
        }
    }
}

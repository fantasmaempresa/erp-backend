<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Imports\ImportCSV;
use App\Models\Place;
use App\Models\RegistrationProcedureData;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\QueryException;
use App\Models\Procedure;


class RegistrationDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        DB::table('registration_procedure_data')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');

        $import = new ImportCSV(Storage::path('backup_sicom/registro.csv'), delimeter: '|');
        $records = $import->readFile();

        foreach ($records as $record) {
            $procedure = Procedure::where('name', trim($record['Expediente']))->first();
            $stake = Place::where('name', 'like', "%" . trim($record['Lugar']) . "%")->first();
            if (empty($procedure->id)) {
                print_r("salto -------> " . trim($record['Expediente']) . " \n");
                continue;
            }

            try {
                $register = new RegistrationProcedureData();

                // if (empty($record['Fecha'])) {
                //     $register->date = null;
                // } else {
                //     $fecha = Carbon::createFromFormat('d/m/Y', $record['Fecha']);
                //     $register->date = $fecha->format('Y-m-d');
                // }
                $register->date = empty($record['Fecha']) ? null : Carbon::createFromFormat('d/m/Y', explode(' ', $record['Fecha'])[0]);
                $register->inscription = trim($record['Inscripcion']);
                $register->sheets = $record['Fojas'];
                $register->took = $record['Tomo'];
                $register->book = $record['Libro'];
                $register->departure = $record['Predio'];
                $register->folio_real_estate = 'bk';
                $register->folio_electronic_merchant = 'bk';
                $register->nci = 'bk';
                $register->description = $record['Observaciones'];
                $register->procedure_id = $procedure->id;
                $register->place_id = empty($stake) ? 1 : $stake->id;
                $register->user_id = 6;

                $register->save();
            } catch (QueryException $exception) {
                print_r($exception->getMessage());
                continue;
            }
        }
    }
}

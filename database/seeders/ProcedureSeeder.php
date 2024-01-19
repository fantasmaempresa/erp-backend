<?php

namespace Database\Seeders;

use App\Imports\ImportCSV;
use App\Models\Procedure;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProcedureSeeder extends Seeder
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

        $import = new ImportCSV(Storage::path('backup_sicom/expedientes.csv'), delimeter: '|');

        try {
            $records = $import->readFile();

            foreach ($records as $record) {
                $date_proceedings = Carbon::createFromFormat('d/m/Y', $record['FechaExp'])->format('Y-m-d');
                $date = Carbon::createFromFormat('d/m/Y', $record['Fecha'])->format('Y-m-d');
                $procedure = new Procedure();

                $procedure->name = $record['Expediente'];
                $procedure->value_operation = $record['ValorOperacion'];
                $procedure->date_proceedings = $date_proceedings;
                $procedure->instrument = $record['Instrumento'];
                $procedure->date = $date;
                $procedure->volume = $record['Volumen'];
                $procedure->folio_min = $record['Folio1'];
                $procedure->folio_max = $record['Folio2'];
                $procedure->credit = $record['Credito'];
                $procedure->observation = $record['Observaciones'];
                $procedure->operation_id = $record['Operacion'];
                $procedure->user_id = 6;
                $procedure->place_id = $record['Lugar'];
                $procedure->client_id = 1;
                $procedure->staff_id = 3;

                $procedure->save();
                print_r($record);
            }
        } catch (IOException|ReaderNotOpenedException $e) {
            print_r("Fallo seeder ---> ", $e->getMessage());
        }
    }
}

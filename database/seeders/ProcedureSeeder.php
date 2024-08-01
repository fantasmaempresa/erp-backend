<?php

namespace Database\Seeders;

use App\Imports\ImportCSV;
use App\Models\Procedure;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Database\QueryException;
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
        DB::table('procedures')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');

        $import = new ImportCSV(Storage::path('backup_sicom/expedientes.csv'), delimeter: '|');

        $records = $import->readFile();

        foreach ($records as $record) {
            try {
                $date_proceedings = explode(' ', $record['FechaExp']);
                $date = explode(' ', $record['Fecha']);

                if (empty($date[0]) && !empty($date_proceedings[0])) {
                    $date[0] = $date_proceedings[0];
                }
                // print_r($date_proceedings[0]);
                // print_r("\n");
                // print_r($date[0]);

                $date_proceedings = Carbon::createFromFormat('d/m/Y', $date_proceedings[0])->format('Y-m-d');
                $date = Carbon::createFromFormat('d/m/Y', $date[0])->format('Y-m-d');
                $procedure = new Procedure();
                $procedure->name = trim($record['Expediente']);
                $procedure->value_operation = trim($record['ValorOperacion']);
                $procedure->date_proceedings = $date_proceedings;
                $procedure->instrument = trim($record['Instrumento']);
                $procedure->date = $date;
                $procedure->volume = trim($record['Volumen']);
                $procedure->folio_min = trim($record['Folio1']);
                $procedure->folio_max = trim($record['Folio2']);
                $procedure->credit = trim($record['Credito']);
                $procedure->observation = trim($record['Observaciones']);
                $procedure->user_id = 6;
                $procedure->place_id = (int) $record['Lugar'];
                $procedure->client_id = 1;
                $procedure->staff_id = 3;
                $procedure->save();
                
                $procedure->operations()->attach((int)$record['Operacion']);
            } catch (IOException|ReaderNotOpenedException $e) {
                print_r("Fallo seeder ---> ", $e->getMessage());
            } catch (QueryException $exception) {
                print_r("SALTO 1---> \n");
                print_r($record);
                print_r($exception->getMessage());
                print_r("<--------------------> \n");

                continue;
            } catch (InvalidFormatException $exception) {
                print_r("SALTO 2--->\n");
                print_r($record);
                continue;
            }
        }
    }
}


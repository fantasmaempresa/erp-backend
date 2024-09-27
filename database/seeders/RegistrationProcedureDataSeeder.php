<?php

namespace Database\Seeders;

use App\Models\Procedure;
use App\Models\RegistrationProcedureData;
use Illuminate\Database\Seeder;

class RegistrationProcedureDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Obtener todos los procedimientos y cargar la relación
        $procedures = Procedure::with('registrationProcedureData')->get();

        foreach ($procedures as $procedure) {

            if (count($procedure->registrationProcedureData) <= 0) continue; 
                
            
            $registrationData = $procedure->registrationProcedureData;


            // Obtener el registro con url_file no vacío o el primero si no existe
            $recordWithUrlFile = $registrationData->where('url_file', '!=', '')->first();
            print_r("recordWithUrlFile --> \n" );
            print_r($recordWithUrlFile);
            
            if (!$recordWithUrlFile) {
                $recordWithUrlFile = $registrationData->first();
            }
        
            // Agrupar los datos de todos los registros del procedimiento
            $groupedData = $registrationData->map(function ($item) {
                return [
                    'inscription' => $item->inscription,
                    'sheets' => $item->sheets,
                    'took' => $item->took,
                    'book' => $item->book,
                    'departure' => $item->departure,
                    'folio_real_estate' => $item->folio_real_estate,
                    'folio_electronic_merchant' => $item->folio_electronic_merchant,
                    'nci' => $item->nci,
                    'description' => $item->description,
                ];
            })->collapse()->toArray();
            // Actualizar el registro seleccionado
            print_r("registrando procedimiento: " . $procedure->id . " " . " " . json_encode($groupedData) . "\n" );
            $recordWithUrlFile->update(['data' => json_encode($groupedData)]);

            // Eliminar solo los registros duplicados que no sean el seleccionado
            $registrationData->where('id', '!=', $recordWithUrlFile->id)
                ->where('url_file', '=', '')
                ->delete();
        }

    }
}

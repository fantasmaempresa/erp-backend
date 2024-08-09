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


        $procedures = Procedure::with('registrationProcedureData')->get();

        foreach ($procedures as $procedure) {
            $registerData = new RegistrationProcedureData();
            $registerData->procedure_id = $procedure->id;

            $jsonData = [];
            $registerWithoutUrl = [];
            foreach ($procedure->registrationProcedureData as $register) {

                if (!empty($register->url_file)) $registerWithoutUrl[] = $register;

                $jsonData[] = [
                    'inscription' => $register->inscription,
                    'sheets' => $register->sheets,
                    'took' => $register->took,
                    'book' => $register->book,
                    'departure' => $register->departure,
                    'folio_real_estate' => $register->folio_real_estate,
                    'folio_electronic_merchant' => $register->folio_electronic_merchant,
                    'nci' => $register->nci,
                    'description' => $register->description,
                ];

                $register->delete();
            }

            $registerData->data = $jsonData;
            $registerData->save();
        }
    }
}

<?php

namespace Database\Seeders;

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
        
        $records = RegistrationProcedureData::all();

        foreach ($records as $record) {
            $jsonData = [
                'inscription' => $record->inscription,
                'sheets' => $record->sheets,
                'took' => $record->took,
                'book' => $record->book,
                'departure' => $record->departure,
                'folio_real_estate' => $record->folio_real_estate,
                'folio_electronic_merchant' => $record->folio_electronic_merchant,
                'nci' => $record->nci,
            ];

            $record->data = [$jsonData];
            $record->save();
        }
    }
}

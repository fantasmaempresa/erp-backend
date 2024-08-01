<?php

namespace Database\Seeders;

use App\Models\Folio;
use Illuminate\Database\Seeder;

class FixMinFolio extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //OBTENEMOS LOS FOLIOS DONDE FOLIO_MIN ES NULL
        $folios = Folio::whereNull('folio_min')->get();
        foreach ($folios as $folio) {
            $folio->folio_min = $folio->folio_max;
            $folio->save();
        }

        print_r("FOLIOS ACTUALIZADOS CON ÉXITO \n");
    }
}

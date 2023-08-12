<?php

namespace Database\Seeders;

use App\Models\Place;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $places = [
            ['name' => 'SIN LUGAR'],
            ['name' => 'PUEBLA, PUE.'],
            ['name' => 'SAN ANDRES CHOLULA, PUEBLA'],
            ['name' => 'SAN PEDRO CHOLULA, PUEBLA'],
            ['name' => 'ATLIXCO, PUEBLA'],
            ['name' => 'CUAUTLANCINGO, PUEBLA'],
            ['name' => 'HUEJOTZINGO, PUEBLA'],
            ['name' => 'CHIAUTEMPAN, TLAXCALA'],
            ['name' => 'CORONANGO, CHOLULA, PUE'],
            ['name' => 'SAN MARTIN TEXMELUCAN, PUE'],
            ['name' => 'VERACRUZ'],
            ['name' => 'SAN MATIAS TLALANCALECA, PUE'],
            ['name' => 'YUCATÁN'],
            ['name' => 'TEHUACAN, PUEBLA'],
            ['name' => 'TECAMACHALCO, PUE'],
            ['name' => 'TEPEACA, PUEBLA'],
            ['name' => 'SAN PEDRO TLALTENANGO, CHOLULA, PUE'],
            ['name' => 'ESTADO DE MEXICO'],
            ['name' => 'ACAJETE, PUE'],
            ['name' => 'GUADALAJARA, JALISCO'],
            ['name' => 'DISTRITO FEDERAL'],
            ['name' => 'SAN GREGORIO ATZOMPA, CHIPILO, PUE'],
            ['name' => 'TLAXCALA'],
            ['name' => 'OAXACA'],
            ['name' => 'TIJUANA'],
            ['name' => 'EXTRANJERO'],
            ['name' => 'CHIGNAHUAPAN, PUE'],
            ['name' => 'SAN GREGORIO ATZOMPA, CHOLULA PUEBLA'],
            ['name' => 'SAN MIGUEL XOXTLA, CHOLULA PUEBLA'],
            ['name' => 'JUAN C. BONILLA, CHOLULA, PUE'],
            ['name' => 'TECALI DE HERRERA PUEBLA'],
            ['name' => 'CHIAPAS'],
            ['name' => 'TEZIUTLAN, PUE'],
            ['name' => 'CIUDAD SERDAN, PUE'],
            ['name' => 'CHOLULA, PUEBLA'],
            ['name' => 'CUYOACO, PUEBLA. DISTRITO LIBRES, PUEBLA'],
            ['name' => 'TLATLAUQUITEPEC, PUEBLA.'],
            ['name' => 'OCOYUCAN, CHOLULA, PUE'],
            ['name' => 'CHIHUAHUA'],
            ['name' => 'SANTA RITA TLAHUAPAN, PUE'],
            ['name' => 'MONTERREY, NUEVO LEON'],
            ['name' => 'HUAUCHINANGO, PUE'],
            ['name' => 'CHIETLA, IZUCAR DE MATAMOROS'],
            ['name' => 'ZACATLÁN, PUE'],
            ['name' => 'MATAMOROS, PUEBLA'],
            ['name' => 'CHIAUTLA DE TAPIA, PUEBLA'],
            ['name' => 'ACATLAN DE OSORIO, PUE'],
            ['name' => 'XICOTEPEC, PUEBLA'],
            ['name' => 'ACAPULCO, GUERRERO'],
            ['name' => 'CORDOBA, VERACRUZ'],
            ['name' => 'FORTIN DE LAS FLORES, VERACRUZ'],
            ['name' => 'LIBRES, PUEBLA'],
            ['name' => 'SONORA'],
            ['name' => 'TEPEXI DE RODRIGUEZ, PUEBLA.'],
            ['name' => 'ZIHUATANEJO, GUERRERO'],
            ['name' => 'YAONAHUAC, PUEBLA'],
            ['name' => 'TETELA DE OCAMPO, PUEBLA'],
            ['name' => 'ZACAPOAXTLA'],
            ['name' => 'CANCUN, QUINTANA ROO'],
            ['name' => 'ORIZABA, VERACRUZ'],
            ['name' => 'CIUDAD DE MEXICO'],
            ['name' => 'SANTA CLARA OCOYUCAN,  PUE'],
            ['name' => 'AMOZOC'],
            ['name' => 'CHACHAPA, AMOZOC'],
            ['name' => 'CUAUTINCHAN, PUEBLA'],
        ];

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('places')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        foreach ($places as $place) {
            print_r('Creando plantilla de lugares');
            print_r($place);
            print_r("--- \n");
            Place::create($place);
        }
    }
}

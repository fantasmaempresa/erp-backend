<?php

namespace Database\Seeders;

use App\Models\Document;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('documents')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $documents = [
            ['name' => 'BOLETA DE INSCRIPCIÓN', 'description' => 'BOLETA DE INSCRIPCIÓN', 'quote' => 0],
            ['name' => 'GENERALES', 'description' => 'GENERALES', 'quote' => 0],
            ['name' => 'ESCRITURA ORIGINAL', 'description' => 'ESCRITURA ORIGINAL', 'quote' => 0],
            ['name' => 'BOLETA PREDIAL', 'description' => 'BOLETA PREDIAL', 'quote' => 0],
            ['name' => 'CONSTANCIA DE NO ADEUDO PREDIAL', 'description' => 'CONSTANCIA DE NO ADEUDO PREDIAL', 'quote' => 0],
            ['name' => 'CONSTANCIA DE NO ADEUDO AGUA', 'description' => 'CONSTANCIA DE NO ADEUDO AGUA', 'quote' => 0],
            ['name' => 'AVALUO', 'description' => 'AVALUO', 'quote' => 0],
            ['name' => 'ACTA DE NACIMIENTO', 'description' => 'ACTA DE NACIMIENTO', 'quote' => 0],
            ['name' => 'ACTA DE MATRIMONIO', 'description' => 'ACTA DE MATRIMONIO', 'quote' => 0],
            ['name' => 'CURP', 'description' => 'CURP', 'quote' => 0],
            ['name' => 'CEDULA FISCAL', 'description' => 'CEDULA FISCAL', 'quote' => 0],
            ['name' => 'IDENTIFICACION O DOCUMENTO MIGRATORIO', 'description' => 'IDENTIFICACION O DOCUMENTO MIGRATORIO', 'quote' => 0],
            ['name' => 'LICENCIA DE CONSTRUCCIÓN O PREEXISTENCIA', 'description' => 'LICENCIA DE CONSTRUCCIÓN O PREEXISTENCIA', 'quote' => 0],
            ['name' => 'ALINEAMIENTO Y NÚMERO OFICIAL', 'description' => 'ALINEAMIENTO Y NÚMERO OFICIAL', 'quote' => 0],
            ['name' => 'CONSTANCIA DE USO DE SUELO', 'description' => 'CONSTANCIA DE USO DE SUELO', 'quote' => 0],
            ['name' => 'TERMINACION DE OBRA', 'description' => 'TERMINACION DE OBRA', 'quote' => 0],
            ['name' => 'PLANOS O LEVANTAMIENTO TOPOGRAFICO', 'description' => 'PLANOS O LEVANTAMIENTO TOPOGRAFICO', 'quote' => 0],
            ['name' => 'TABLA DE INDIVISOS O ANEXO "B"', 'description' => 'TABLA DE INDIVISOS O ANEXO "B"', 'quote' => 0],
            ['name' => 'REGLAMENTO DE CONDOMINOS', 'description' => 'REGLAMENTO DE CONDOMINOS', 'quote' => 0],
            ['name' => 'MEMORIA DESCRIPTIVA', 'description' => 'MEMORIA DESCRIPTIVA', 'quote' => 0],
            ['name' => 'PERMISO DE SUBDIVISION', 'description' => 'PERMISO DE SUBDIVISION', 'quote' => 0],
            ['name' => 'PERMISO DE FUSION', 'description' => 'PERMISO DE FUSION', 'quote' => 0],
            ['name' => 'ACTA CONSTITUTIVA', 'description' => 'ACTA CONSTITUTIVA', 'quote' => 0],
            ['name' => 'PODER/DOCUS PARA ACREDITAR PERSONALIDAD', 'description' => 'PODER/DOCUS PARA ACREDITAR PERSONALIDAD', 'quote' => 0],
            ['name' => 'PERMISO SRE EXTRANJEROS', 'description' => 'PERMISO SRE EXTRANJEROS', 'quote' => 0],
            ['name' => 'CARTA DE INSTRUCCION', 'description' => 'CARTA DE INSTRUCCION', 'quote' => 0],
            ['name' => 'COMPROBANTE DOMICILIARIO', 'description' => 'COMPROBANTE DOMICILIARIO', 'quote' => 0],
            ['name' => 'EXPEDIENTE DE JUZGADO', 'description' => 'EXPEDIENTE DE JUZGADO', 'quote' => 0],
            ['name' => 'NO ADEUDO AGUA O FACTIBILIDAD', 'description' => 'NO ADEUDO AGUA O FACTIBILIDAD', 'quote' => 0],
            ['name' => 'LICENCIA DE CONSTRUCCION O PREEXISTENCIA', 'description' => 'LICENCIA DE CONSTRUCCION O PREEXISTENCIA', 'quote' => 0],
            ['name' => 'ALINEAMIENTO Y NUMERO OFICIAL', 'description' => 'ALINEAMIENTO Y NUMERO OFICIAL', 'quote' => 0],
            ['name' => 'PLANOS O VENTAMIENTO TOPOGRAFICO', 'description' => 'PLANOS O VENTAMIENTO TOPOGRAFICO', 'quote' => 0],
            ['name' => 'AUTORIZACIÓN DE DENOMINACION', 'description' => 'AUTORIZACIÓN DE DENOMINACION', 'quote' => 0],
        ];

        foreach ($documents as $document) {
            print_r('Creando documento');
            print_r($document);
            print_r("--- \n");
            Document::create($document);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Operation;
use Illuminate\Database\Seeder;

class NewOperationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $newOperations = [
            ['name' => "AMPLIACION DE CONSTRUCCION", 'description' => 'AMPLIACION DE CONSTRUCCION',],
            ['name' => "APLICACION DE BIENES HEREDITARIOS - JUZGADO", 'description' => 'APLICACION DE BIENES HEREDITARIOS - JUZGADO',],
            ['name' => "APLICACION DE BIENES HEREDITARIOS", 'description' => 'APLICACION DE BIENES HEREDITARIOS',],
            ['name' => "ASOCIACION CIVIL", 'description' => 'ASOCIACION CIVIL',],
            ['name' => "CANCELACION DE RESERVA DE DOMINIO", 'description' => 'CANCELACION DE RESERVA DE DOMINIO',],
            ['name' => "COMPRA VENTA A PLAZOS CON RESERVA DE DOMINIO", 'description' => 'COMPRA VENTA A PLAZOS CON RESERVA DE DOMINIO',],
            ['name' => "COMPRA VENTA JUDICIAL", 'description' => 'COMPRA VENTA JUDICIAL',],
            ['name' => "COMPRA VENTA", 'description' => 'COMPRA VENTA',],
            ['name' => "CONSOLIDACION DE PROPIEDAD", 'description' => 'CONSOLIDACION DE PROPIEDAD',],
            ['name' => "DACION EN PAGO", 'description' => 'DACION EN PAGO',],
            ['name' => "DECLARACION DE CONSTRUCCION", 'description' => 'DECLARACION DE CONSTRUCCION',],
            ['name' => "DONACION CON RESERVA DE USUFRUCTO VITALICIO", 'description' => 'DONACION CON RESERVA DE USUFRUCTO VITALICIO',],
            ['name' => "DONACION", 'description' => 'DONACION',],
            ['name' => "FUSION", 'description' => 'FUSION',],
            ['name' => "INSCRIPCION DE NOMBRAMIENTO DE ALBACEA", 'description' => 'INSCRIPCION DE NOMBRAMIENTO DE ALBACEA',],
            ['name' => "LOTIFICACION", 'description' => 'LOTIFICACION',],
            ['name' => "PERMUTA", 'description' => 'PERMUTA',],
            ['name' => "PROTO INVENTARIOS Y AVALUOS", 'description' => 'PROTO INVENTARIOS Y AVALUOS',],
            ['name' => "RECTIFICACION", 'description' => 'RECTIFICACION',],
            ['name' => "REGIMEN DE PROPIEDAD EN CONDOMINIO", 'description' => 'REGIMEN DE PROPIEDAD EN CONDOMINIO',],
            ['name' => "S.A. DE C.V.", 'description' => 'S.A. DE C.V.',],
            ['name' => "SEGREGACION", 'description' => 'SEGREGACION',],
            ['name' => "SERVIDUMBRE DE PASO", 'description' => 'SERVIDUMBRE DE PASO',],
            ['name' => "SOCIEDAD CIVIL", 'description' => 'SOCIEDAD CIVIL',],
            ['name' => "SOFOM", 'description' => 'SOFOM',],
        ];

        foreach ($newOperations as $newOperation) {
            print_r('Creando Operación');
            print_r($newOperation);
            print_r("--- \n");
            Operation::create($newOperation);
        }
    }
}

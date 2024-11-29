<?php

/*
 * OPEN2CODE 2023
 */

namespace Database\Seeders;

use App\Models\Shape;
use App\Models\TemplateShape;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * TEMPLATE SHAPE SEEDER
 */
class TemplateShapeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $templateShapes = [
            [
                'id' => 1,
                'name' => 'Forma-01',
                'form' => [
                    ['name' => 'property', 'type' => 'text', 'label' => 'Indice de Predio'],
                    ['name' => 'value_catastral', 'type' => 'text', 'label' => 'Valor Catastral'],
                    ['name' => 'domicile', 'type' => 'text', 'label' => 'Domicilio para notificar al adquiriente'],
                    [
                        'name' => 'alienated_property_street',
                        'type' => 'text',
                        'label' => 'Calle del inmueble',
                    ],
                    [
                        'name' => 'alienated_property_outdoor_number',
                        'type' => 'text',
                        'label' => 'NoExt del inmueble',
                    ],
                    [
                        'name' => 'alienated_property_interior_number',
                        'type' => 'text',
                        'label' => 'NoInt del inmueble',
                    ],
                    [
                        'name' => 'alienated_property_colony',
                        'type' => 'text',
                        'label' => 'Colonia del inmueble',
                    ],
                    [
                        'name' => 'alienated_property_locality',
                        'type' => 'text',
                        'label' => 'Localidad del inmueble',
                    ],
                    [
                        'name' => 'alienated_property_municipality',
                        'type' => 'text',
                        'label' => 'Municipio del inmueble',
                    ],
                    [
                        'name' => 'alienated_property_entity',
                        'type' => 'text',
                        'label' => 'Entidad del inmueble',
                    ],
                    [
                        'name' => 'alienated_property_zipcode',
                        'type' => 'number',
                        'label' => 'Código Postal del inmueble',
                    ],
                    ['name' => 'sold', 'type' => 'text', 'label' => 'Superficie vendida'],
                    ['name' => 'remaining', 'type' => 'text', 'label' => 'Superficie restante'],
                    ['name' => 'built', 'type' => 'text', 'label' => 'Superficie construida'],
                    ['name' => 'use', 'type' => 'text', 'label' => 'Uso predominante del inmueble'],
                    ['name' => 'value_catastral', 'type' => 'text', 'label' => 'Valor catastral'],
                    ['name' => 'rate', 'type' => 'text', 'label' => 'Tasa %'],
                    ['name' => 'tax', 'type' => 'text', 'label' => 'Impuesto Actualizado'],
                    ['name' => 'alienating_surcharges', 'type' => 'text', 'label' => 'Recargos por extemporeneidad'],
                    // ['name' => 'acquirer_surcharges', 'type' => 'text', 'label' => 'Recargos Adquiriente'],
                    ['name' => 'total_tax', 'type' => 'text', 'label' => 'Total de Impuestos'],
                    ['name' => 'alienating_basis', 'type' => 'text', 'label' => 'Fundamento legal (renglon 1)'],
                    ['name' => 'acquirer_basis', 'type' => 'text', 'label' => 'Fundameto legal (renglo 2)'],
                    [
                        'name' => 'alienated_property_basis',
                        'type' => 'text',
                        'label' => 'Fundamento legal (renglo 3)',
                    ],
                    [
                        'name' => 'alienating_quote_basis',
                        'type' => 'text',
                        'label' => 'Cuota (renglon 1)',
                    ],
                    [
                        'name' => 'acquirer_quote_basis',
                        'type' => 'text',
                        'label' => 'Cuota (renglon 2)',
                    ],
                    [
                        'name' => 'alienated_property_quote_basis',
                        'number' => 'text',
                        'label' => 'Cuota (renglon 3)',
                    ],
                    ['name' => 'total_rights', 'type' => 'text', 'label' => 'Total de Derechos'],
                    ['name' => 'total_tax_rights', 'type' => 'text', 'label' => 'Total de Impuestos y Derechos'],
                ],
            ],
            [
                'id' => 2,
                'name' => 'Forma-02',
                'form' => [
                    ['name' => 'type', 'type' => 'text', 'label' => 'Tipo de Predio'],
                    ['name' => 'index', 'type' => 'text', 'label' => 'Indice de Predio'],
                    ['name' => 'property', 'type' => 'text', 'label' => 'Descripción del inmueble'],
                    [
                        'name' => 'alienated_property_street',
                        'type' => 'text',
                        'label' => 'Calle del inmueble',
                    ],
                    [
                        'name' => 'alienated_property_outdoor_number',
                        'type' => 'text',
                        'label' => 'NoExt del inmueble',
                    ],
                    [
                        'name' => 'alienated_property_interior_number',
                        'type' => 'text',
                        'label' => 'NoInt del inmueble',
                    ],
                    [
                        'name' => 'alienated_property_colony',
                        'type' => 'text',
                        'label' => 'Colonia del inmueble',
                    ],
                    [
                        'name' => 'alienated_property_locality',
                        'type' => 'text',
                        'label' => 'Localidad del inmueble',
                    ],
                    [
                        'name' => 'alienated_property_municipality',
                        'type' => 'text',
                        'label' => 'Municipio del inmueble',
                    ],
                    [
                        'name' => 'alienated_property_entity',
                        'type' => 'text',
                        'label' => 'Entidad federativa del inmueble',
                    ],
                    [
                        'name' => 'alienated_property_zipcode',
                        'type' => 'number',
                        'label' => 'Código Postal del inmueble',
                    ],
                    [
                        'name' => 'alienated_property_phone',
                        'type' => 'phone_number',
                        'label' => 'Teléfono del inmueble',
                    ],

                    ['name' => 'first_article', 'type' => 'text', 'label' => 'Articulo (Renglón 1)'],
                    ['name' => 'legal_section', 'type' => 'text', 'label' => 'Apartado (Renglón 1)'],
                    ['name' => 'first_fraction', 'type' => 'text', 'label' => 'Fraccion (Renglón 1)'],
                    ['name' => 'first_quote', 'type' => 'text', 'label' => 'Cuota (Renglón 1)'],
                    ['name' => 'firs_amount', 'type' => 'text', 'label' => 'Importe (Renglón 1)'],
                    ['name' => 'firs_modification_settlement', 'type' => 'text', 'label' => 'Modificación de la Liquidación (Renglón 1)'],

                    ['name' => 'second_article', 'type' => 'text', 'label' => 'Articulo (Renglón 2)'],
                    ['name' => 'second_section', 'type' => 'text', 'label' => 'Apartado (Renglón 2)'],
                    ['name' => 'second_fraction', 'type' => 'text', 'label' => 'Fraccion (Renglón 2)'],
                    ['name' => 'second_quote', 'type' => 'text', 'label' => 'Cuota (Renglón 2)'],
                    ['name' => 'second_amount', 'type' => 'text', 'label' => 'Importe (Renglón 1)'],
                    ['name' => 'second_modification_settlement', 'type' => 'text', 'label' => 'Modificación de la Liquidación (Renglón 2)'],
                    
                    ['name' => 'thirty_article', 'type' => 'text', 'label' => 'Articulo (Renglón 3)'],
                    ['name' => 'thirty_section', 'type' => 'text', 'label' => 'Apartado (Renglón 3)'],
                    ['name' => 'thirty_fraction', 'type' => 'text', 'label' => 'Fraccion (Renglón 3)'],
                    ['name' => 'thirty_quote', 'type' => 'text', 'label' => 'Cuota (Renglón 3)'],
                    ['name' => 'thirty_amount', 'type' => 'text', 'label' => 'Importe (Renglón 3)'],
                    ['name' => 'thirty_modification_settlement', 'type' => 'text', 'label' => 'Modificación de la Liquidación (Renglón 3)'],

                    ['name' => 'fourth_article', 'type' => 'text', 'label' => 'Articulo (Renglón 4)'],
                    ['name' => 'fourth_section', 'type' => 'text', 'label' => 'Apartado (Renglón 4)'],
                    ['name' => 'fourth_fraction', 'type' => 'text', 'label' => 'Fraccion (Renglón 4)'],
                    ['name' => 'fourth_quote', 'type' => 'text', 'label' => 'Cuota (Renglón 4)'],
                    ['name' => 'fourth_amount', 'type' => 'text', 'label' => 'Importe (Renglón 4)'],
                    ['name' => 'fourth_modification_settlement', 'type' => 'text', 'label' => 'Modificación de la Liquidación (Renglón 4)'],

                    //TOTALES
                    ['name' => 'total', 'type' => 'text', 'label' => 'Total Pago de derechos'],
                    ['name' => 'total_modification_settlement', 'type' => 'text', 'label' => 'Total de la Modificación de la Liquidación'],

                ],
            ],
            [
                'id' => 3,
                'name' => 'Forma-02 Catastro del Estado de Puebla',
                'form' => [
                    ['name' => 'type', 'type' => 'text', 'label' => 'Tipo de Predio'],
                    ['name' => 'index', 'type' => 'text', 'label' => 'Indice de Predio'],
                    ['name' => 'property', 'type' => 'text', 'label' => 'Ubicación actual del inmueble'],
                    [
                        'name' => 'alienated_property_home',
                        'type' => 'text',
                        'label' => 'Domicilio para notificar',
                    ],
                    ['name' => 'first_article', 'type' => 'text', 'label' => 'Articulo (Renglón 1)'],
                    ['name' => 'legal_section', 'type' => 'text', 'label' => 'Apartado (Renglón 1)'],
                    ['name' => 'first_fraction', 'type' => 'text', 'label' => 'Fraccion (Renglón 1)'],
                    ['name' => 'first_quote', 'type' => 'text', 'label' => 'Cuota (Renglón 1)'],
                    ['name' => 'firs_amount', 'type' => 'text', 'label' => 'Importe (Renglón 1)'],

                    ['name' => 'second_article', 'type' => 'text', 'label' => 'Articulo (Renglón 2)'],
                    ['name' => 'second_section', 'type' => 'text', 'label' => 'Apartado (Renglón 2)'],
                    ['name' => 'second_fraction', 'type' => 'text', 'label' => 'Fraccion (Renglón 2)'],
                    ['name' => 'second_quote', 'type' => 'text', 'label' => 'Cuota (Renglón 2)'],
                    ['name' => 'second_amount', 'type' => 'text', 'label' => 'Importe (Renglón 1)'],

                    ['name' => 'thirty_article', 'type' => 'text', 'label' => 'Articulo (Renglón 3)'],
                    ['name' => 'thirty_section', 'type' => 'text', 'label' => 'Apartado (Renglón 3)'],
                    ['name' => 'thirty_fraction', 'type' => 'text', 'label' => 'Fraccion (Renglón 3)'],
                    ['name' => 'thirty_quote', 'type' => 'text', 'label' => 'Cuota (Renglón 3)'],
                    ['name' => 'thirty_amount', 'type' => 'text', 'label' => 'Importe (Renglón 3)'],

                    ['name' => 'fourth_article', 'type' => 'text', 'label' => 'Articulo (Renglón 4)'],
                    ['name' => 'fourth_section', 'type' => 'text', 'label' => 'Apartado (Renglón 4)'],
                    ['name' => 'fourth_fraction', 'type' => 'text', 'label' => 'Fraccion (Renglón 4)'],
                    ['name' => 'fourth_quote', 'type' => 'text', 'label' => 'Cuota (Renglón 4)'],
                    ['name' => 'fourth_amount', 'type' => 'text', 'label' => 'Importe (Renglón 4)'],
                ],
            ],
            [
                'id' => 4,
                'name' => 'Forma-02 Catastro del Municipio de Puebla',
                'form' => [
                    ['name' => 'type', 'type' => 'text', 'label' => 'Tipo de Predio'],
                    ['name' => 'index', 'type' => 'text', 'label' => 'Indice de Predio'],

                    //DIRECCIÓN DEL INMUEBLE
                    ['name' => 'property', 'type' => 'text', 'label' => 'Descripción del inmueble'],
                    [
                        'name' => 'alienated_property_home',
                        'type' => 'text',
                        'label' => 'Domicilio para notificar',
                    ],


                    [
                        'name' => 'alienated_property_street',
                        'type' => 'text',
                        'label' => 'Calle del inmueble',
                    ],
                    [
                        'name' => 'alienated_property_outdoor_number',
                        'type' => 'text',
                        'label' => 'NoExt del inmueble',
                    ],
                    [
                        'name' => 'alienated_property_interior_number',
                        'type' => 'text',
                        'label' => 'NoInt del inmueble',
                    ],
                    [
                        'name' => 'alienated_property_colony',
                        'type' => 'text',
                        'label' => 'Colonia del inmueble',
                    ],
                    [
                        'name' => 'alienated_property_locality',
                        'type' => 'text',
                        'label' => 'Localidad del inmueble',
                    ],
                    [
                        'name' => 'alienated_property_municipality',
                        'type' => 'text',
                        'label' => 'Municipio del inmueble',
                    ],
                    [
                        'name' => 'alienated_property_entity',
                        'type' => 'text',
                        'label' => 'Entidad federativa del inmueble',
                    ],
                    [
                        'name' => 'alienated_property_zipcode',
                        'type' => 'number',
                        'label' => 'Código Postal del inmueble',
                    ],
                    [
                        'name' => 'alienated_property_zipcode',
                        'type' => 'phone_number',
                        'label' => 'Teléfono del inmueble',
                    ],
                    //DIRECCIÓN DEL INMUEBLE


                    ['name' => 'first_article', 'type' => 'text', 'label' => 'Articulo (Renglón 1)'],
                    ['name' => 'legal_section', 'type' => 'text', 'label' => 'Apartado (Renglón 1)'],
                    ['name' => 'first_fraction', 'type' => 'text', 'label' => 'Fraccion (Renglón 1)'],
                    ['name' => 'first_quote', 'type' => 'text', 'label' => 'Cuota (Renglón 1)'],
                    ['name' => 'firs_amount', 'type' => 'text', 'label' => 'Importe (Renglón 1)'],
                    //NUEVO CAMPO MODIFICACIÓN DE LA LIQUIDACIÓN
                    ['name' => 'firs_modification_settlement', 'type' => 'text', 'label' => 'Modificación de la Liquidación (Renglón 1)'],

                    ['name' => 'second_article', 'type' => 'text', 'label' => 'Articulo (Renglón 2)'],
                    ['name' => 'second_section', 'type' => 'text', 'label' => 'Apartado (Renglón 2)'],
                    ['name' => 'second_fraction', 'type' => 'text', 'label' => 'Fraccion (Renglón 2)'],
                    ['name' => 'second_quote', 'type' => 'text', 'label' => 'Cuota (Renglón 2)'],
                    ['name' => 'second_amount', 'type' => 'text', 'label' => 'Importe (Renglón 2)'],
                    //NUEVO CAMPO MODIFICACIÓN DE LA LIQUIDACIÓN
                    ['name' => 'second_modification_settlement', 'type' => 'text', 'label' => 'Modificación de la Liquidación (Renglón 2)'],

                    ['name' => 'thirty_article', 'type' => 'text', 'label' => 'Articulo (Renglón 3)'],
                    ['name' => 'thirty_section', 'type' => 'text', 'label' => 'Apartado (Renglón 3)'],
                    ['name' => 'thirty_fraction', 'type' => 'text', 'label' => 'Fraccion (Renglón 3)'],
                    ['name' => 'thirty_quote', 'type' => 'text', 'label' => 'Cuota (Renglón 3)'],
                    ['name' => 'thirty_amount', 'type' => 'text', 'label' => 'Importe (Renglón 3)'],
                    //NUEVO CAMPO MODIFICACIÓN DE LA LIQUIDACIÓN
                    ['name' => 'thirty_modification_settlement', 'type' => 'text', 'label' => 'Modificación de la Liquidación (Renglón 3)'],

                    ['name' => 'fourth_article', 'type' => 'text', 'label' => 'Articulo (Renglón 4)'],
                    ['name' => 'fourth_section', 'type' => 'text', 'label' => 'Apartado (Renglón 4)'],
                    ['name' => 'fourth_fraction', 'type' => 'text', 'label' => 'Fraccion (Renglón 4)'],
                    ['name' => 'fourth_quote', 'type' => 'text', 'label' => 'Cuota (Renglón 4)'],
                    ['name' => 'fourth_amount', 'type' => 'text', 'label' => 'Importe (Renglón 4)'],
                    //NUEVO CAMPO MODIFICACIÓN DE LA LIQUIDACIÓN
                    ['name' => 'fourth_modification_settlement', 'type' => 'text', 'label' => 'Modificación de la Liquidación (Renglón 4)'],
                    //NUEVO CAMPO MODIFICACIÓN DE LA LIQUIDACIÓN
                    //NUEVO CAMPO MODIFICACIÓN DE LA LIQUIDACIÓN
                    ['name' => 'total_modification_settlement', 'type' => 'text', 'label' => 'Total de la Modificación de la Liquidación'],
                    //NUEVO CAMPO MODIFICACIÓN DE LA LIQUIDACIÓN
                    ['name' => 'total', 'type' => 'text', 'label' => 'Total Pago de derechos'],
                ],
            ],
        ];

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('template_shapes')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        foreach ($templateShapes as $templateShape) {
            print_r('Creando plantilla de forma');
            print_r($templateShape);
            print_r("--- \n");
            TemplateShape::create($templateShape);
        }
    }
}

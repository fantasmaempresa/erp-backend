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
                    ['name' => 'property', 'type' => 'text', 'label' => 'Predio'],
                    ['name' => 'value_catastral', 'type' => 'number', 'label' => 'Valor Catastral'],
                    ['name' => 'domicile', 'type' => 'text', 'label' => 'Domicilio'],
                    [
                        'name' => 'alienated_property_street',
                        'type' => 'text',
                        'label' => 'Calle del inmueble enagenado',
                    ],
                    [
                        'name' => 'alienated_property_outdoor_number',
                        'type' => 'text',
                        'label' => 'NoExt del inmueble enagenado',
                    ],
                    [
                        'name' => 'alienated_property_interior_number',
                        'type' => 'text',
                        'label' => 'NoInt del inmueble enagenado ',
                    ],
                    [
                        'name' => 'alienated_property_colony',
                        'type' => 'text',
                        'label' => 'Colonia del inmueble enagenado',
                    ],
                    [
                        'name' => 'alienated_property_locality',
                        'type' => 'text',
                        'label' => 'Localidad del inmueble enagenado',
                    ],
                    [
                        'name' => 'alienated_property_municipality',
                        'type' => 'text',
                        'label' => 'Municipio del inmueble enagenado',
                    ],
                    [
                        'name' => 'alienated_property_entity',
                        'type' => 'text',
                        'label' => 'Entidad del inmueble enagenado',
                    ],
                    [
                        'name' => 'alienated_property_zipcode',
                        'type' => 'number',
                        'label' => 'Código Postal del inmueble enagenado',
                    ],
                    ['name' => 'sold', 'type' => 'text', 'label' => 'Vendida'],
                    ['name' => 'remaining', 'type' => 'text', 'label' => 'Restante'],
                    ['name' => 'built', 'type' => 'text', 'label' => 'Construida'],
                    ['name' => 'use', 'type' => 'text', 'label' => 'Uso'],
                    ['name' => 'value_catastral', 'type' => 'text', 'label' => 'Valor catastral'],
                    ['name' => 'rate', 'type' => 'text', 'label' => 'Tasa'],
                    ['name' => 'tax', 'type' => 'text', 'label' => 'Impuesto'],
                    ['name' => 'alienating_surcharges', 'type' => 'text', 'label' => 'Recargos Enajenante'],
                    ['name' => 'acquirer_surcharges', 'type' => 'text', 'label' => 'Recargos Adquiriente'],
                    ['name' => 'total_tax', 'type' => 'number', 'label' => 'Total de Impuestos'],
                    ['name' => 'alienating_basis', 'type' => 'text', 'label' => 'Fundamento  Enajenante'],
                    [
                        'name' => 'alienating_quote_basis',
                        'number' => 'text',
                        'label' => 'Cuota de Fundamento Enajenante',
                    ],
                    ['name' => 'acquirer_basis', 'type' => 'text', 'label' => 'Fundameto Adquiriente'],
                    [
                        'name' => 'acquirer_quote_basis',
                        'type' => 'number',
                        'label' => 'Cuota de fundamento Adquiriente',
                    ],
                    [
                        'name' => 'alienated_property_basis',
                        'type' => 'text',
                        'label' => 'Fundamento del inmueble enagenado',
                    ],
                    [
                        'name' => 'alienated_property_quote_basis',
                        'number' => 'text',
                        'label' => 'Cuota del Fundamento del inmueble enagenado',
                    ],
                    ['name' => 'total_tax_rights', 'type' => 'text', 'label' => 'Total de Impuestos y Derechos'],
                ],
            ],
            [
                'id' => 2,
                'name' => 'Forma-02',
                'form' => [
                    ['name' => 'type', 'type' => 'text', 'label' => 'Tipo'],
                    ['name' => 'index', 'type' => 'number', 'label' => 'Indice'],
                    ['name' => 'property', 'type' => 'text', 'label' => 'Inmueble'],
                    [
                        'name' => 'alienated_property_home',
                        'type' => 'text',
                        'label' => 'Domicilio de la propiedad a enajenar',
                    ],
                    ['name' => 'first_article', 'type' => 'text', 'label' => 'Primer Articulo'],
                    ['name' => 'legal_section', 'type' => 'text', 'label' => 'Primer Apartado'],
                    ['name' => 'first_fraction', 'type' => 'text', 'label' => 'Fraccion'],
                    ['name' => 'first_quote', 'type' => 'text', 'label' => 'Primer Cuota'],
                    ['name' => 'firs_amount', 'type' => 'text', 'label' => 'Primer Importe'],
                    ['name' => 'second_article', 'type' => 'number', 'label' => 'Articulo2'],
                    ['name' => 'second_section', 'type' => 'number', 'label' => 'Apartado2'],
                    ['name' => 'second_fraction', 'type' => 'text', 'label' => 'Fraccion2'],
                    ['name' => 'second_quote', 'type' => 'text', 'label' => 'Cuota2'],
                    ['name' => 'second_amount', 'type' => 'text', 'label' => 'Importe2'],
                    ['name' => 'thirty_article', 'type' => 'text', 'label' => 'Articulo3'],
                    ['name' => 'thirty_section', 'type' => 'text', 'label' => 'Apartado3'],
                    ['name' => 'thirty_fraction', 'type' => 'text', 'label' => 'Fraccion3'],
                    ['name' => 'thirty_quote', 'type' => 'text', 'label' => 'Cuota3'],
                    ['name' => 'thirty_amount', 'type' => 'text', 'label' => 'Importe3'],
                    ['name' => 'fourth_article', 'type' => 'number', 'label' => 'Articulo4'],
                    ['name' => 'fourth_section', 'type' => 'text', 'label' => 'Apartado4'],
                    ['name' => 'fourth_fraction', 'type' => 'text', 'label' => 'Fraccion4'],
                    ['name' => 'fourth_quote', 'type' => 'text', 'label' => 'Cuota4'],
                    ['name' => 'fourth_amount', 'type' => 'text', 'label' => 'Importe4'],
                ],
            ],
            [
                'id' => 3,
                'name' => 'Forma-02 de declaración de modificación de los datos asentados en el registro catatral del predio',
                'form' => [
                    ['name' => 'type', 'type' => 'text', 'label' => 'Tipo'],
                    ['name' => 'index', 'type' => 'number', 'label' => 'Indice'],
                    ['name' => 'property', 'type' => 'text', 'label' => 'Inmueble'],
                    [
                        'name' => 'alienated_property_home',
                        'type' => 'text',
                        'label' => 'Domicilio de la propiedad a enajenar',
                    ],
                    ['name' => 'first_article', 'type' => 'text', 'label' => 'Primer Articulo'],
                    ['name' => 'legal_section', 'type' => 'text', 'label' => 'Primer Apartado'],
                    ['name' => 'first_fraction', 'type' => 'text', 'label' => 'Fraccion'],
                    ['name' => 'first_quote', 'type' => 'text', 'label' => 'Primer Cuota'],
                    ['name' => 'firs_amount', 'type' => 'text', 'label' => 'Primer Importe'],
                    ['name' => 'second_article', 'type' => 'number', 'label' => 'Articulo2'],
                    ['name' => 'second_section', 'type' => 'number', 'label' => 'Apartado2'],
                    ['name' => 'second_fraction', 'type' => 'text', 'label' => 'Fraccion2'],
                    ['name' => 'second_quote', 'type' => 'text', 'label' => 'Cuota2'],
                    ['name' => 'second_amount', 'type' => 'text', 'label' => 'Importe2'],
                    ['name' => 'thirty_article', 'type' => 'text', 'label' => 'Articulo3'],
                    ['name' => 'thirty_section', 'type' => 'text', 'label' => 'Apartado3'],
                    ['name' => 'thirty_fraction', 'type' => 'text', 'label' => 'Fraccion3'],
                    ['name' => 'thirty_quote', 'type' => 'text', 'label' => 'Cuota3'],
                    ['name' => 'thirty_amount', 'type' => 'text', 'label' => 'Importe3'],
                    ['name' => 'fourth_article', 'type' => 'number', 'label' => 'Articulo4'],
                    ['name' => 'fourth_section', 'type' => 'text', 'label' => 'Apartado4'],
                    ['name' => 'fourth_fraction', 'type' => 'text', 'label' => 'Fraccion4'],
                    ['name' => 'fourth_quote', 'type' => 'text', 'label' => 'Cuota4'],
                    ['name' => 'fourth_amount', 'type' => 'text', 'label' => 'Importe4'],
                ],
            ],
            [
                'id' => 4,
                'name' => 'Forma-02 secretaría de tesorería municipal de Puebla',
                'form' => [
                    ['name' => 'type', 'type' => 'text', 'label' => 'Tipo'],
                    ['name' => 'index', 'type' => 'number', 'label' => 'Indice'],
                    ['name' => 'property', 'type' => 'text', 'label' => 'Inmueble'],
                    [
                        'name' => 'alienated_property_home',
                        'type' => 'text',
                        'label' => 'Domicilio de la propiedad a enajenar',
                    ],
                    ['name' => 'first_article', 'type' => 'text', 'label' => 'Primer Articulo'],
                    ['name' => 'legal_section', 'type' => 'text', 'label' => 'Primer Apartado'],
                    ['name' => 'first_fraction', 'type' => 'text', 'label' => 'Fraccion'],
                    ['name' => 'first_quote', 'type' => 'text', 'label' => 'Primer Cuota'],
                    ['name' => 'firs_amount', 'type' => 'text', 'label' => 'Primer Importe'],
                    ['name' => 'second_article', 'type' => 'number', 'label' => 'Articulo2'],
                    ['name' => 'second_section', 'type' => 'number', 'label' => 'Apartado2'],
                    ['name' => 'second_fraction', 'type' => 'text', 'label' => 'Fraccion2'],
                    ['name' => 'second_quote', 'type' => 'text', 'label' => 'Cuota2'],
                    ['name' => 'second_amount', 'type' => 'text', 'label' => 'Importe2'],
                    ['name' => 'thirty_article', 'type' => 'text', 'label' => 'Articulo3'],
                    ['name' => 'thirty_section', 'type' => 'text', 'label' => 'Apartado3'],
                    ['name' => 'thirty_fraction', 'type' => 'text', 'label' => 'Fraccion3'],
                    ['name' => 'thirty_quote', 'type' => 'text', 'label' => 'Cuota3'],
                    ['name' => 'thirty_amount', 'type' => 'text', 'label' => 'Importe3'],
                    ['name' => 'fourth_article', 'type' => 'number', 'label' => 'Articulo4'],
                    ['name' => 'fourth_section', 'type' => 'text', 'label' => 'Apartado4'],
                    ['name' => 'fourth_fraction', 'type' => 'text', 'label' => 'Fraccion4'],
                    ['name' => 'fourth_quote', 'type' => 'text', 'label' => 'Cuota4'],
                    ['name' => 'fourth_amount', 'type' => 'text', 'label' => 'Importe4'],
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

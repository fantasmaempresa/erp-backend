<?php

namespace Database\Seeders;

use App\Models\CategoryOperation;
use Illuminate\Database\Seeder;

class CategoryOperationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        print_r("Creando Categorias de Operaciones  \n");

        CategoryOperation::create([
            "name" => "Trasalado de Dominio",
            "description" => "Trasalado de Dominio",
            "config" => [
                "vulnerable" => [
                    [
                        "type" => CategoryOperation::UMA,
                        "condition" => 16000
                    ]
                ],
            ]
        ]);

        CategoryOperation::create([
            "name" => "Constitución, Aumento, Disminución o Venta de Acciones",
            "description" => "Constitución, Aumento, Disminución o Venta de Acciones",
            "config" => [
                "vulnerable" => [
                    [
                        "type" => CategoryOperation::UMA,
                        "condition" => 16000
                    ]
                ]
            ]
        ]);

        CategoryOperation::create([
            "name" => "Otorgamiento de Poderes",
            "description" => "Otorgamiento de Poderes",
            "config" => [
                "vulnerable" => [
                    [
                        "type" => CategoryOperation::OPTION,
                        "condition" => 'irrevocable'
                    ]
                ]
            ]
        ]);

        CategoryOperation::create([
            "name" => "Constitución y Modifiación de Fideicomiso",
            "description" => "Constitución y Modifiación de Fideicomiso",
            "config" => [
                "vulnerable" => [
                    [
                        "type" => CategoryOperation::UMA,
                        "condition" => 8025
                    ]
                ]
            ]
        ]);

        CategoryOperation::create([
            "name" => "Otorgamiento de motivo o crédito",
            "description" => "Otorgamiento de motivo o crédito",
            "config" => [
                "vulnerable" => [
                    [
                        "type" => CategoryOperation::DOCUMENT,
                        "condition" => [
                            [
                                "id" => 1,
                            ]
                        ]
                    ]
                ]
            ]
        ]);
    }
}

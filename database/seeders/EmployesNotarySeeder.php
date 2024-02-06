<?php

namespace Database\Seeders;

use App\Models\Staff;
use App\Models\WorkArea;
use Illuminate\Database\Seeder;

class EmployesNotarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        print_r("Creando Áreas de la Notaría  \n");

        WorkArea::create(
            [
                'id' => 3,
                'name' => 'Dirección Administrativa',
                'description' => 'Dirección Administrativa',
                'config' => [],
            ]
        );

        WorkArea::create(
            [
                'id' => 4,
                'name' => 'Gestoria',
                'description' => 'Gestoria',
                'config' => [],
            ]
        );


        WorkArea::create(
            [
                'id' => 5,
                'name' => 'Recepción',
                'description' => 'Recepción',
                'config' => [],
            ]
        );

        WorkArea::create(
            [
                'id' => 6,
                'name' => 'Caja y contabilidad',
                'description' => 'Caja y contabilidad',
                'config' => [],
            ]
        );

        WorkArea::create(
            [
                'id' => 7,
                'name' => 'Proyectistas',
                'description' => 'Proyectistas',
                'config' => [],
            ]
        );

        WorkArea::create(
            [
                'id' => 8,
                'name' => 'Control',
                'description' => 'Control',
                'config' => [],
            ]
        );

        WorkArea::create(
            [
                'id' => 9,
                'name' => 'Asesores',
                'description' => 'Asesores',
                'config' => [],
            ]
        );

        WorkArea::create(
            [
                'id' => 10,
                'name' => 'Archivo',
                'description' => 'Archivo',
                'config' => [],
            ]
        );

        WorkArea::create(
            [
                'id' => 11,
                'name' => 'Auxiliar de Proyectista',
                'description' => 'Auxiliar de Proyectistas',
                'config' => [],
            ]
        );

        print_r("Creando empleados y usuarios \n");


        Staff::create([
            'id' => 4,
            'name' => 'Verónica Ivonne',
            'last_name' => 'Tellez',
            'mother_last_name' => 'Suárez',
            'email' => 'ivonne.suarez@notaria4puebla.com.mx',
            'phone' => '2226754552',
            'nickname' => 'Ivonne',
            'extra_information' => [],
            'work_area_id' => 7,
            'user_id' => 7,
        ]);

        Staff::create([
            'id' => 5,
            'name' => 'Marco Antonio',
            'last_name' => 'Flores',
            'mother_last_name' => 'Gil',
            'email' => 'contabilidad@notaria4puebla.com.mx',
            'phone' => '2224265154',
            'nickname' => 'Conta Marco',
            'extra_information' => [],
            'work_area_id' => 6,
            'user_id' => 8,
        ]);

        Staff::create([
            'id' => 6,
            'name' => 'Yhasmin',
            'last_name' => 'Pérez',
            'mother_last_name' => 'Pérez',
            'email' => 'yhasmin.perez@notaria4puebla.com.mx',
            'phone' => '2481739059',
            'nickname' => 'Yhas',
            'extra_information' => [],
            'work_area_id' => 10,
            'user_id' => 9,
        ]);

        Staff::create([
            'id' => 7,
            'name' => 'Sebastian',
            'last_name' => 'Vargas',
            'mother_last_name' => 'Barrera',
            'email' => 'sebastian.barrera@notaria4puebla.com.mx',
            'phone' => '2222553102',
            'nickname' => 'Sebas',
            'extra_information' => [],
            'work_area_id' => 8,
            'user_id' => 10,
        ]);

        Staff::create([
            'id' => 8,
            'name' => 'Lesly Paola',
            'last_name' => 'Martínez',
            'mother_last_name' => 'Montiel',
            'email' => 'lesly_martinez@notaria4puebla.com.mx',
            'phone' => '2214373550',
            'nickname' => 'Lesly',
            'extra_information' => [],
            'work_area_id' => 5,
            'user_id' => 11,
        ]);

        Staff::create([
            'id' => 9,
            'name' => 'Montserrat',
            'last_name' => 'Rodríguez',
            'mother_last_name' => 'Salamanca',
            'email' => 'montserrat_rodriguez@notaria4puebla.com.mx',
            'phone' => '2481795919',
            'nickname' => 'Monse',
            'extra_information' => [],
            'work_area_id' => 8,
            'user_id' => 12,
        ]);

        Staff::create([
            'id' => 10,
            'name' => 'María Fernanda',
            'last_name' => 'Aguila',
            'mother_last_name' => 'Zempoaltecatl',
            'email' => 'maria_aguila@notaria4puebla.com.mx',
            'phone' => '2226954897',
            'nickname' => 'Fer',
            'extra_information' => [],
            'work_area_id' => 5,
            'user_id' => 13,
        ]);

        Staff::create([
            'id' => 11,
            'name' => 'Jose Pablo',
            'last_name' => 'Rabines',
            'mother_last_name' => 'Toxqui',
            'email' => 'jose_rabines@notaria4puebla.com.mx',
            'phone' => '2227196511',
            'nickname' => 'Pablo',
            'extra_information' => [],
            'work_area_id' => 11,
            'user_id' => 14,
        ]);

        Staff::create([
            'id' => 12,
            'name' => 'Ana Lariza',
            'last_name' => 'Juárez',
            'mother_last_name' => 'Herrera',
            'email' => 'lariza_juarez@notaria4puebla.com.mx',
            'phone' => '2228134908',
            'nickname' => 'Lary',
            'extra_information' => [],
            'work_area_id' => 11,
            'user_id' => 15,
        ]);

        Staff::create([
            'id' => 13,
            'name' => 'Edith',
            'last_name' => 'Salamanca',
            'mother_last_name' => 'Contreras',
            'email' => 'edith.salamanca@notaria4puebla.com.mx',
            'phone' => '2484894833',
            'nickname' => 'Conta Edith',
            'extra_information' => [],
            'work_area_id' => 10,
            'user_id' => 16,
        ]);

        Staff::create([
            'id' => 14,
            'name' => 'Elda Rosa',
            'last_name' => 'Rios',
            'mother_last_name' => 'Melendez',
            'email' => 'elda.rios@notaria4puebla.com.mx',
            'phone' => '2227501790',
            'nickname' => 'Mtra. Elda',
            'extra_information' => [],
            'work_area_id' => 3,
            'user_id' => 17,
        ]);

        Staff::create([
            'id' => 15,
            'name' => 'José',
            'last_name' => 'Pellegrin',
            'mother_last_name' => 'Romero',
            'email' => 'jose.pellegrin@notaria4puebla.com.mx',
            'phone' => '2221253911',
            'nickname' => 'Josesito',
            'extra_information' => [],
            'work_area_id' => 9,
            'user_id' => 18,
        ]);

    }
}

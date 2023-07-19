<?php

namespace Database\Seeders;

use App\Models\Staff;
use App\Models\WorkArea;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('work_areas')->truncate();
        DB::table('staff')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        WorkArea::create(
            [
                'id' => 1,
                'name' => 'Notario Titular',
                'description' => 'Notario Titular',
                'config' => [],
            ]
        );

        WorkArea::create(
            [
                'id' => 2,
                'name' => 'Notario Auxiliar',
                'description' => 'Notario Auxiliar',
                'config' => [],
            ]
        );

        Staff::create([
            'id' => 1,
            'name' => 'Norma',
            'last_name' => 'Romero',
            'mother_last_name' => 'Cortes',
            'email' => 'normaromero@notaria4puebla.com.mx',
            'phone' => '2221714964',
            'nickname' => 'Notario Mtra. Norma',
            'extra_information' => [],
            'work_area_id' => 1,
            'user_id' => 2,
        ]);

        Staff::create([
            'id' => 2,
            'name' => 'Norma Alma',
            'last_name' => 'Cortes',
            'mother_last_name' => 'Caballero',
            'email' => 'normacortes@notaria4puebla.com.mx',
            'phone' => '2221714965',
            'nickname' => 'Notario Auxiliar Mtra. Norma',
            'extra_information' => [],
            'work_area_id' => 2,
            'user_id' => 3,
        ]);

    }
}

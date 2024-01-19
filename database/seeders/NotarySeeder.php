<?php

namespace Database\Seeders;

use App\Models\Client;
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
        DB::table('clients')->truncate();
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

        Staff::create([
            'id' => 3,
            'name' => 'backup',
            'last_name' => 'sicom',
            'mother_last_name' => ' ',
            'email' => 'backup@sicom.com.mx',
            'phone' => '2221714967',
            'nickname' => 'Usuario para ligar el backup de sicom',
            'extra_information' => [],
            'work_area_id' => 2,
            'user_id' => 6,
        ]);

        Client::create([
            'id' => 1,
            'name' => 'backup sicon',
            'last_name' => 'backup sicon',
            'mother_last_name' => 'backup sicon',
            'email' => 'backup_sicon@backup_sicon.com',
            'phone' => '2221714958',
            'nickname' => '',
            'address' => '',
            'rfc' => '',
            'type' => 2,
            'profession' => null,
            'degree' => null,
            'extra_information' => null,
            'user_id' => null,
        ]);
    }
}

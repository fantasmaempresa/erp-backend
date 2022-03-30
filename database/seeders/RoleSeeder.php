<?php
/*
 * CODE
 * Role Seeder Class
 */

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * @access  public
 *
 * @version 1.0
 */
class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('roles')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        DB::table('roles')->insert([
            'id' => Role::$ADMIN,
            'name' => 'Administrador',
            'description' => 'Tiene control de la plataforma completa',
            'config' => null,
        ]);

        DB::table('roles')->insert([
            'id' => Role::$USER,
            'name' => 'Usuario',
            'description' => '',
            'config' => null,
        ]);
    }
}

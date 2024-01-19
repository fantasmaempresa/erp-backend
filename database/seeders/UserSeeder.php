<?php

/*
 * CODE
 * User Seeder Class
 */

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * @access  public
 *
 * @version 1.0
 */
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('users')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        DB::table('users')->insert([
            'id' => 1,
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('secret'),
            'role_id' => Role::$ADMIN,
        ]);

        DB::table('users')->insert([
            'id' => 2,
            'name' => 'Norma Romero Cortes',
            'email' => 'norma@admin.com',
            'password' => bcrypt('secret'),
            'role_id' => Role::$ADMIN,
        ]);

        DB::table('users')->insert([
            'id' => 3,
            'name' => 'Norma Alma Cortes Caballero',
            'email' => 'normac@admin.com',
            'password' => bcrypt('secret'),
            'role_id' => Role::$ADMIN,
        ]);

        DB::table('users')->insert([
            'id' => 4,
            'name' => 'alex',
            'email' => 'metalico900@gmail.com',
            'password' => bcrypt('secret'),
            'role_id' => Role::$ADMIN,
        ]);

        DB::table('users')->insert([
            'id' => 5,
            'name' => 'test',
            'email' => 'test@test.com',
            'password' => bcrypt('secret'),
            'role_id' => Role::$USER,
        ]);

        DB::table('users')->insert([
            'id' => 6,
            'name' => 'backup_sicom',
            'email' => 'backup_sicom@backup_sicom.com',
            'password' => bcrypt('secret'),
            'role_id' => Role::$ADMIN,
        ]);
    }
}

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

        DB::table('users')->insert([
            'id' => 7,
            'name' => 'Verónica Ivonne',
            'email' => 'ivonne.suarez@notaria4puebla.com.mx',
            'password' => bcrypt('ivonne.suarez'),
            'role_id' => Role::$USER,
        ]);

        DB::table('users')->insert([
            'id' => 8,
            'name' => 'Marco Antonio Flores Gil',
            'email' => 'contabilidad@notaria4puebla.com.mx',
            'password' => bcrypt('contabilidad'),
            'role_id' => Role::$USER,
        ]);

        DB::table('users')->insert([
            'id' => 9,
            'name' => 'Yhasmin Pérez Pérez',
            'email' => 'yhasmin.perez@notaria4puebla.com.mx',
            'password' => bcrypt('yhasmin.perez'),
            'role_id' => Role::$USER,
        ]);

        DB::table('users')->insert([
            'id' => 10,
            'name' => 'Sebastian Vargas Barrera',
            'email' => 'sebastian.barrera@notaria4puebla.com.mx',
            'password' => bcrypt('sebastian.barrera'),
            'role_id' => Role::$USER,
        ]);


        DB::table('users')->insert([
            'id' => 11,
            'name' => 'Lesly Paola Martínez Montiel',
            'email' => 'lesly_martinez@notaria4puebla.com.mx',
            'password' => bcrypt('lesly_martinez'),
            'role_id' => Role::$USER,
        ]);

        DB::table('users')->insert([
            'id' => 12,
            'name' => 'Montserrat Rodríguez Salamanca',
            'email' => 'montserrat_rodriguez@notaria4puebla.com.mx',
            'password' => bcrypt('montserrat_rodriguez'),
            'role_id' => Role::$USER,
        ]);

        DB::table('users')->insert([
            'id' => 13,
            'name' => 'María Fernanda Aguila Zempoaltecatl',
            'email' => 'maria_aguila@notaria4puebla.com.mx',
            'password' => bcrypt('maria_aguila'),
            'role_id' => Role::$USER,
        ]);


        DB::table('users')->insert([
            'id' => 14,
            'name' => 'Jose Pablo Rabines Toxqui',
            'email' => 'jose_rabines@notaria4puebla.com.mx',
            'password' => bcrypt('jose_rabines'),
            'role_id' => Role::$USER,
        ]);

        DB::table('users')->insert([
            'id' => 15,
            'name' => 'Ana Lariza Juárez Herrera',
            'email' => 'lariza_juarez@notaria4puebla.com.mx',
            'password' => bcrypt('jose_rabines'),
            'role_id' => Role::$USER,
        ]);

        DB::table('users')->insert([
            'id' => 16,
            'name' => 'Edith Salamanca Contreras',
            'email' => 'edith.salamanca@notaria4puebla.com.mx',
            'password' => bcrypt('edith.salamanca'),
            'role_id' => Role::$USER,
        ]);

        DB::table('users')->insert([
            'id' => 17,
            'name' => 'Elda Rosa Rios Melendez',
            'email' => 'elda.rios@notaria4puebla.com.mx',
            'password' => bcrypt('elda.rios'),
            'role_id' => Role::$USER,
        ]);

        DB::table('users')->insert([
            'id' => 18,
            'name' => 'José Pellegrin Romero',
            'email' => 'jose.pellegrin@notaria4puebla.com.mx',
            'password' => bcrypt('elda.rios'),
            'role_id' => Role::$ADMIN,
        ]);
    }
}

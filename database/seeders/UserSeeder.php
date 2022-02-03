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
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('secret'),
            'role_id' => Role::$ADMIN,
        ]);

        DB::table('users')->insert([
            'name' => 'alex',
            'email' => 'metalico900@gmail.com',
            'password' => bcrypt('secret'),
            'role_id' => Role::$ADMIN,
        ]);
    }
}

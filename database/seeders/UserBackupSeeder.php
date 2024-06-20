<?php

namespace Database\Seeders;

use App\Imports\ImportCSV;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserBackupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $import = new ImportCSV(Storage::path('backup_server/users_backup.csv'), delimeter: '|');
        $users = $import->readFile();
        $users = $users->where('id', '>', 18)->values();

        $import = new ImportCSV(Storage::path('backup_server/staff_backup.csv'), delimeter: '|');
        $staff = $import->readFile();
        $staff = $staff->where('id', '>', 15)->values();

        DB::beginTransaction();
        try {
            Staff::where('user_id', 10)->delete();
            Staff::where('user_id', 15)->delete();

            User::where('id', 10)->delete();
            User::where('id', 15)->delete();

            foreach ($users as $user) {
                User::create([
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'password' => $user['password'],
                    'role_id' => $user['role_id'],
                ]);
            }

            foreach ($staff as $staff) {
                Staff::create($staff);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            print_r('User Backup Error: ' . $e->getMessage());
        }
    }
}

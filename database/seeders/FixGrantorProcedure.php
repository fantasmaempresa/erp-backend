<?php

namespace Database\Seeders;

use App\Models\Grantor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FixGrantorProcedure extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('alter table grantor_procedure add column stake_id bigint(20) unsigned NOT NULL after procedure_id;');

        DB::transaction(function () {
            Grantor::chunk(1000, function ($grantors) {
                foreach ($grantors as $grantor) {
                    $procedures = $grantor->procedures;
                    foreach ($procedures as $procedure) {
                        $grantor->procedures()->updateExistingPivot($procedure->id, ['stake_id' => $grantor->stake_id]);
                    }
                }
            });
        });

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::statement('alter table grantors drop foreign key grantors_stake_id_foreign');
        DB::statement('alter table grantors drop column stake_id');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        print_r("Grantor Procedure Seeded");
        print_r("\n");
    }
}

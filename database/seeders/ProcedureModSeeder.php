<?php

namespace Database\Seeders;

use App\Models\Procedure;
use Illuminate\Database\Seeder;

class ProcedureModSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $procedures = Procedure::all();
        foreach ($procedures as $procedure) {
            $procedure->operations()->attach([$procedure->operation_id]);
            $procedure->save();
        }
    }
}

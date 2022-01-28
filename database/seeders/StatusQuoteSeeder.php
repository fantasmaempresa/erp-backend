<?php

namespace Database\Seeders;

use App\Models\StatusQuote;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusQuoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('status_quotes')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        DB::table('status_quotes')->insert([
            'id' => StatusQuote::$START,
            'name' => 'Cotización creada',
            'description' => 'En este estado la cotización es revisada por el administrador',
        ]);

        DB::table('status_quotes')->insert([
            'id' => StatusQuote::$REVIEW,
            'name' => 'Cotización en revición',
            'description' => 'La cotización tiene que ser revisada por el usuario quien la creó',
        ]);

        DB::table('status_quotes')->insert([
            'id' => StatusQuote::$APPROVED,
            'name' => 'Cotización aprobada',
            'description' => 'Cotización aprobada por el administrador',
        ]);

        DB::table('status_quotes')->insert([
            'id' => StatusQuote::$FINISH,
            'name' => 'Cotización terminada',
            'description' => 'Cotización se dio como terminada porque el proyecto se marco como finalizado',
        ]);
    }
}

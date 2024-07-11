<?php

namespace Database\Seeders;

use App\Models\Shape;
use Illuminate\Database\Seeder;

class FixShapeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $shapes = Shape::all();
        foreach ($shapes as $shape) {
            if(!is_array($shape->data_form)){
                $shape->data_form = json_decode($shape->data_form, true);
                $shape->save();
            }
        }
    }
}

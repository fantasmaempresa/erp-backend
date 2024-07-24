<?php

namespace Database\Seeders;

use App\Models\Folio;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FolioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Query to get procedures and their corresponding books
        $procedureBookQuery = DB::query()
            ->select('procedures.id', 'procedures.user_id', 'procedures.folio_min', 'procedures.folio_max', 'procedures.instrument', 'books.id AS book_id')
            ->from('procedures')
            ->join('books', 'procedures.volume', '=', 'books.name');

        // Execute the query and fetch results
        $procedureBookData = $procedureBookQuery->get();
        // Process each procedure-book pair and insert data into folios
        foreach ($procedureBookData as $data) {
            
            if($data->instrument == 0) continue;
            // Insert data into the folios table
            $folio = new Folio();
            $folio->book_id = $data->book_id;
            $folio->procedure_id = $data->id;
            $folio->folio_min = $data->folio_min;
            $folio->folio_max = $data->folio_max;
            $folio->name = $data->instrument;
            $folio->user_id = $data->user_id;
            $folio->save();
            
        }
    }
}

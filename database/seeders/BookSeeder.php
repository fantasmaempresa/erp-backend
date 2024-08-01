<?php

namespace Database\Seeders;

use App\Models\Book;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $uniqueBooksQuery = DB::query()
            ->select('volume', DB::raw('MIN(folio_min) AS folio_min_range'), DB::raw('MAX(folio_max) AS folio_max_range'))
            ->from('procedures')
            ->groupBy('volume')
            ->orderBy('volume');

        // Execute the query and fetch results
        $uniqueBooksWithRanges = $uniqueBooksQuery->get();

        // Prepare data for insertion into the new table
        $newTableRows = [];

        foreach ($uniqueBooksWithRanges as $bookData) {
            $book = new Book();
            $book->name = $bookData->volume; //volume
            $book->folio_min = $bookData->folio_min_range;
            $book->folio_max = $bookData->folio_max_range;
            $book->date_proceedings = Carbon::now();
            $book->save();
        }
    }
}

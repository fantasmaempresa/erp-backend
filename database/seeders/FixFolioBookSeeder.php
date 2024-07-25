<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\BookDocument;
use App\Models\Folio;
use App\Models\Procedure;
use Illuminate\Database\Seeder;

class FixFolioBookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //OBTENEMOS EL RANGO DE VOLUMENES
        $books = Book::whereNotBetween('name', [476, 689])->get();

        //RECORREMOS LOS VOLUMENES PARA ELIMINAR LOS INSTRUMENTOS
        foreach ($books as $book) {
            Folio::where('book_id', $book->id)->delete();
            BookDocument::where('book_id', $book->id)->delete();
            $book->delete();
        }

        //OBTENEMOS EL ÚLTIMO REGISTRO DE PROCEDURES
        $procedure = Procedure::orderBy('id', 'desc')->first();

        //OBTENEMOS EL REGISTRO MAXIMO DE INSTRUMENTO
        $folio = Folio::where('procedure_id', $procedure->id)->first();

        //OBTENEMOS EL RANGO DE INSTRUMENTOS
        Folio::whereNotBetween('name', [38639, (int)$folio->name])->delete();

        print_r("VOLUMENES E INSTRUMENTOS ELIMINADOS CON EXITO \n");
    }
}

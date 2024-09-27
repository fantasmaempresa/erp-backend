<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Seeder;

class BookRangeVerify extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $lastBook = Book::orderBy('id', 'desc')->first();

        // Inicializar el siguiente folio mínimo esperado
        $expectedMax = $lastBook->folio_max;

        // Obtener todos los registros ordenados descendentemente por folio_max
        $books = Book::orderBy('id', 'desc')->get();

        foreach ($books as $book) {
            if($book->name == 531) break;
            // Calcular el rango correcto
            $expectedMin = $expectedMax - 149;

            // Actualizar el registro si es necesario
            if ($book->folio_min !== $expectedMin || $book->folio_max !== $expectedMax) {
                $book->folio_min = $expectedMin;
                $book->folio_max = $expectedMax;
                $book->save();
            }

            // Actualizar el expectedMax para la siguiente iteración
            $expectedMax -= 150;
        }
    
    }
}

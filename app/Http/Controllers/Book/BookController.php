<?php

namespace App\Http\Controllers\Book;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Folio\FolioUtil;
use App\Models\Book;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BookController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $paginate = empty($request->get('paginate')) ? env('NUMBER_PAGINATE') : $request->get('paginate');

        if (!empty($request->get('search')) && $request->get('search') !== 'null') {
            $query = Book::search($request->get('search'));
        } else {
            $query = Book::query();
        }

        $response = $query->orderBy('name', 'desc')->paginate($paginate);


        return $this->showList($response);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, Book::rules());

        $book = new Book($request->all());
        $book->date_proceedings = Carbon::parse($book->date_proceedings);

        if (FolioUtil::verifyRangeFolio(new Book(), $book->folio_min, $book->folio_max)) {
            $book->save();
        } else {
            return $this->errorResponse('Los folios estan fuera de rango', 409);
        }

        return $this->showOne($book);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function show(Book $book)
    {
        return $this->showOne($book);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Book $book)
    {
        $this->validate($request, Book::rules($book->id));
        
        $book->fill($request->all());
        
        if ($book->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }

        $book->date_proceedings = Carbon::parse($book->date_proceedings);
        
        if (FolioUtil::verifyRangeFolio(new Book(), $book->folio_min, $book->folio_max, $book->id)) {
            $book->save();
        } else {
            return $this->errorResponse('Los folios estan fuera de rango', 409);
        }
        
        $book->save();

        return $this->showOne($book);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function destroy(Book $book)
    {
        $book->delete();

        return $this->showMessage('Se elimino con éxito');
    }
}

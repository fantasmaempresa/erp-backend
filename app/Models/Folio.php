<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Folio extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'folio_min',
        'folio_max',
        'book_id',
        'procedure_id',
        'user_id',
    ];

    public function procedure(){
        return $this->belongsTo(Procedure::class);
    }

    public function book(){
        return $this->belongsTo(Book::class);
    }
}

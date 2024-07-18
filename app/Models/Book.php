<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'folio_min',
        'folio_max',
        'date_proceedings',
    ];

    public function scopeSearch($query, $search): mixed
    {
        return $query->orWhere('name', 'like', "%$search%")
            ->orWhere('folio_min', $search)
            ->orWhere('folio_max', $search)
            ->orWhere('date_proceedings', $search);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function folios()
    {
        return $this->hasMany(Folio::class);
    }


    static function rules($id = null)
    {

        $rules = [
            'name' => 'required|string|unique:books,name',
            'folio_min' => 'nullable|string|unique:books,folio_min',
            'folio_max' => 'required|string|unique:books,folio_max',
            'date_proceedings' => 'required|date',
        ];

        if ($id) {
            $rules['name'] = ['required', Rule::unique('books')->ignore($id)];
            $rules['folio_min'] = ['required', Rule::unique('books')->ignore($id)];
            $rules['folio_max'] = ['required', Rule::unique('books')->ignore($id)];
        }

        return $rules;
    }
}

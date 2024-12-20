<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class Folio extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name', //instrument -> procedure
        'folio_min',
        'folio_max',
        'unused_folios',
        'integrate_appendix',
        'config',
        'book_id',
        'procedure_id',
        'user_id',
    ];

    protected $casts = [
        'unused_folios' => 'array',
        'config' => 'array'
    ];

    public function scopeSearch($query, $search): mixed
    {
        return $query->orWhere('name', $search)
            ->orWhere('folio_min', $search)
            ->orWhere('folio_max', $search);
    }
    public function scopeAdvanceFilter($query, $filters)
    {

        if (!empty($filters->only_errors)) {
            $query->where('unused_folios', '<>', null);
        }

        if (!empty($filters->only_unassigned)) {
            $query->where('procedure_id', null);
        }
    }

    public function procedure()
    {
        return $this->belongsTo(Procedure::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    static function rules($id = null)
    {

        $rules = [
            'name' => 'required|int|unique:folios,name',
            'folio_min' => 'required|int|unique:folios,folio_min',
            'folio_max' => 'required|int|unique:folios,folio_max',
            'book_id' => 'required|int',
            'procedure_id' => 'nullable|int|unique:folios,procedure_id',
            'integrate_appendix' => 'nullable|boolean',
            'config' => 'nullable|array',
        ];

        if ($id) {
            $rules['name'] = ['required', Rule::unique('folios')->ignore($id)];
            $rules['folio_min'] = ['required', Rule::unique('folios')->ignore($id)];
            $rules['folio_max'] = ['required', Rule::unique('folios')->ignore($id)];
            $rules['procedure_id'] = ['nullable', Rule::unique('folios')->ignore($id)];
        }

        return $rules;
    }
}

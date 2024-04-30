<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class Unit extends Model
{
    protected $fillable = [
        'id',
        'year',
        'value'
    ];

    public function scopeSearch($query, $search)
    {
        return $query->orWhere('year', 'like', "%$search%");
    }

    public static function rules($id = null)
    {
        $rules = [
            'year' => 'required|int|unique:units',
            'value' => 'required|numeric'
        ];

        if ($id) {
            $rules['year'] = [
                'required',
                'integer',
                Rule::unique('units')->ignore($id)
            ];
        }

        return $rules;
    }
}

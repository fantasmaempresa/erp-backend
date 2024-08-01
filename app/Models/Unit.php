<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class Unit extends Model
{
    protected $fillable = [
        'id',
        'name',
        'value'
    ];

    public function scopeSearch($query, $search)
    {
        return $query->orWhere('name', 'like', "%$search%");
    }

    public static function rules($id = null)
    {
        $rules = [
            'name' => 'required|int|unique:units',
            'value' => 'required|numeric'
        ];

        if ($id) {
            $rules['name'] = [
                'required',
                'integer',
                Rule::unique('units')->ignore($id)
            ];
        }

        return $rules;
    }
}

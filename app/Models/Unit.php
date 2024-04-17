<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class Unit extends Model
{
    protected $fillable = [
        'id',
        'name',
        'description',
        'year',
        'value'
    ];

    public function scopeSearch($query, $search)
    {
        return $query->orWhere('name', 'like', "%$search%")
            ->orWhere('description', 'like', "%$search%");
    }

    public static function rules($id = null)
    {
        $rules = [
            'name' => 'required|string|unique:units',
            'description' => 'required|string',
            'year' => 'required|int',
            'value' => 'required|numeric'
        ];

        if ($id) {
            $rules['name'] = [
                'required',
                'string',
                Rule::unique('units')->ignore($id)
            ];
        }

        return $rules;
    }
}

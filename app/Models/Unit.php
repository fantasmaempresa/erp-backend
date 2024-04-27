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

    public static function rules()
    {
        return [
            'year' => 'required|int',
            'value' => 'required|numeric'
        ];
    }
}

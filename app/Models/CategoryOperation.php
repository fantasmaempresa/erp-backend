<?php

namespace App\Models;

use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;

class CategoryOperation extends Model
{
    protected $fillable = [
        'id',
        'name',
        'description',
        'config',
        'form'
    ];

    protected $casts = [
        'config' => 'array',
        'form' => 'array'
    ];

    public function operation()
    {
        return $this->hasMany(Operation::class);
    }

    public function scopeSearch($query, $search)
    {
        return $query->orWhere('name', 'like', "%$search%")
            ->orWhere('description', 'like', "%$search%");
    }

    public static function rules($id = null)
    {
        $rules = [
            'name' => 'required|string|unique:category_operations',
            'description' => 'required|string',
            'config' => 'required|array',
            'form' => 'required|array',
        ];

        if($id){
            $rules['name'] = [
                'required',
                'string',
                Rule::unique('category_operations')->ignore($id)
            ];
        }

        return $rules;
    }
}

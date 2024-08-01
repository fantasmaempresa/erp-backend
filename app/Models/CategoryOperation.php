<?php

namespace App\Models;

use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;

class CategoryOperation extends Model
{

    const UMA = 1;
    const UDI = 2;
    const DOCUMENT = 3;
    const OPTION = 4;
 
    protected $fillable = [
        'id',
        'name',
        'description',
        'config',
        'general_template_id'
    ];

    protected $casts = [
        'config' => 'array',
        // 'form' => 'array'
    ];

    public function operation()
    {
        return $this->hasMany(Operation::class);
    }

    public function generalTemplate()
    {
        return $this->belongsTo(GeneralTemplate::class);
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
            'config' => 'nullable|array',
            'general_template_id' => 'nullable|exists:general_templates,id',
            'documents.*.id' => 'required|exists:documents,id',
        ];

        if ($id) {
            $rules['name'] = [
                'required',
                'string',
                Rule::unique('category_operations')->ignore($id)
            ];
        }

        return $rules;
    }
}

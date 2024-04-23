<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VulnerableOperation extends Model
{
    protected $fillable = [
        'id',
        'data_form',
        'procedure_id',
        'unit_id',
    ];

    protected $casts = [
        'data_form' => 'array',
    ];

    public function procedure()
    {
        return $this->belongsTo(Procedure::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function documents()
    {
        return $this->belongsToMany(Document::class)->withPivot(['file', 'id']);
    }

    public static function rules()
    {

        return [
            'data_form' => 'required|array',
            'procedure_id' => 'required|exists:procedures,id',
            'unit_id' => 'required|exists:units,id',
        ];
    }
}

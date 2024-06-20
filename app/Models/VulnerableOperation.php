<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VulnerableOperation extends Model
{
    protected $fillable = [
        'id',
        'type_category',
        'type_vulnerable_operation',
        'grantor_first_id',
        'grantor_second_id',
        'vulnerable_operation_data',
        'procedure_id',
        'unit_id',
        'inversion_unit_id'
    ];

    protected $casts = [
        'grantor_first_id' => 'array',
        'grantor_second_id' => 'array',
        'vulnerable_operation_data' => 'array',
    ];

    public function procedure()
    {
        return $this->belongsTo(Procedure::class);
    }

    public function unit(){
        return $this->belongsTo(Unit::class);
    }

    public function inversionUnit(){
        return $this->belongsTo(Unit::class);
    }

    public function documents()
    {
        return $this->belongsToMany(Document::class)->withPivot(['file', 'id']);
    }

    public function scopeSearch($query, $search){
        return $query
                ->join('procedures', 'vulnerable_operations.procedure_id', '=', 'procedures.id')
                ->where('procedures.name', 'like', '%'.$search.'%')
                ->select('vulnerable_operations.*');
    }

    public static function rules()
    {
        return [
            'procedure_id' => 'required|exists:procedures,id',
            'unit_id' => 'nullable|exists:units,id',
            'inversion_unit_id' => 'nullable|exists:inversion_units,id',
        ];
    }
}

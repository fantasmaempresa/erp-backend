<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VulnerableOperation extends Model
{
    protected $fillable = [
        'id',
        'data_form',
        'capital',
        'constitution',
        'increase',
        'capital_decrease',
        'sale_shares',
        'way_to_pay',
        'real_estate_folio',
        'meters_land',
        'construction_meters',
        'property_type',
        'procedure_id',
        'unit_id',
        'inversion_unit_id'
    ];

    protected $casts = [
        'data_form' => 'array',
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
            'data_form' => 'required|array',
            'procedure_id' => 'required|exists:procedures,id',
            'unit_id' => 'nullable|exists:units,id',
            'inversion_unit_id' => 'nullable|exists:units,id',
        ];
    }
}

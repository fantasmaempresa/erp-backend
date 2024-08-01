<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Validation\Rule;

class Article extends Model
{
    use HasFactory;

    const ACTIVE = 1;
    const CONSUMABLE = 2;

    protected $fillable
        = [
            'id',
            'billable',
            'bar_code',
            'description',
            'name',
            'image',
            'line_id',
            'purchase_cost',
            'sale_cost',
            'type',
            'brand',
            'storable',
            'purchase_measure_unit',
            'sale_measure_unit',
        ];
    
    protected function setBarCodeAttribute($value){
        $this->attributes['bar_code'] = strtolower($value);
    }
    
    protected function getBarCodeAttribute($value){
        return strtoupper($value);
    }

    protected function setBarDescriptionAttribute($value){
        $this->attributes['description'] = strtolower($value);
    }
    
    protected function getBarDescriptionAttribute($value){
        return strtoupper($value);
    }

    protected function setNameAttribute($value){
        $this->attributes['name'] = strtolower($value);
    }
    
    protected function getNameAttribute($value){
        return strtoupper($value);
    }

    protected function setBrandAttribute($value){
        $this->attributes['brand'] = strtolower($value);
    }
    
    protected function getBrandAttribute($value){
        return strtoupper($value);
    }

    protected function setPurchaseMeasureUnitAttribute($value){
        $this->attributes['purchase_measure_unit'] = strtolower($value);
    }
    
    protected function getPurchaseMeasureUnitAttribute($value){
        return strtoupper($value);
    }

    protected function setSaleMeashureUnitAttribute($value){
        $this->attributes['sale_measure_unit'] = strtolower($value);
    }
    
    protected function getSaleMeasureUnitAttribute($value){
        return strtoupper($value);
    }

    public static function rules($id = null, $type): array
    {
        $rule = [
            'billable' => 'required|boolean',
            'bar_code' => [
                'unique:articles,bar_code',
                Rule::requiredIf($type == self::ACTIVE)
            ], // Activo,Consumible
            'description' => 'required|string',
            'name' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'line_id' => 'required|exists:lines,id',
            'purchase_cost' => 'nullable|numeric',
            'sale_cost' => 'nullable|numeric',
            'type' => 'required|int',
            'brand' => 'nullable|string',
            'storable' => 'required|boolean',
            'purchase_measure_unit' => 'required|string',
            'sale_measure_unit' => 'required|string',
        ];

        return $rule;
    }

    /**
     * @return BelongsToMany
     */
    public function inventory(): BelongsToMany
    {
        return $this->belongsToMany(Inventory::class);
    }

    /**
     * @return BelongsToMany
     */
    public function movementTracking(): BelongsToMany
    {
        return $this->belongsToMany(MovementTracking::class);
    }


    /**
     * @param $query
     * @param $search
     *
     * @return mixed
     */
    public function scopeSearch($query, $search): mixed
    {
        return $query->orWhere('bar_code', 'like', "%$search%")
            ->orWhere('name', 'like', "%$search%")
            ->orWhere('id_line', 'like', "%$search%")
            ->orWhere('type', 'like', "%$search%")
            ->orWhere('brand', 'like', "%$search%");
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Article extends Model
{
    use HasFactory;

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
            'purchase_meashure_unit',
            'sale_meashure_unit',
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

    protected function setTypeAttribute($value){
        $this->attributes['type'] = strtolower($value);
    }
    
    protected function getTypeAttribute($value){
        return strtoupper($value);
    }

    protected function setBrandAttribute($value){
        $this->attributes['brand'] = strtolower($value);
    }
    
    protected function getBrandAttribute($value){
        return strtoupper($value);
    }

    protected function setPurchaseMeashureUnitAttribute($value){
        $this->attributes['purchase_meashure_unit'] = strtolower($value);
    }
    
    protected function getPurchaseMeashureUnitAttribute($value){
        return strtoupper($value);
    }

    protected function setSaleMeashureUnitAttribute($value){
        $this->attributes['sale_meashure_unit'] = strtolower($value);
    }
    
    protected function getSaleMeashureUnitAttribute($value){
        return strtoupper($value);
    }

    public static function rules($id = null): array
    {
        $rule = [
            'billable' => 'required|boolean',
            'bar_code' => 'required|string',
            'description' => 'required|string',
            'name' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'line_id' => 'required|string',
            'purchase_cost' => 'nullable|float|default:0',
            'sale_cost' => 'nullable|float|default:0',
            'type' => 'required|string|in:Activo,Consumible',
            'brand' => 'nullable|string',
            'storable' => 'required|boolean',
            'purchase_meashure_unit' => 'required|string',
            'sale_meashure_unit' => 'required|string',
        ];
        $rule['bar_code'] .='|unique:type,Activo';
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

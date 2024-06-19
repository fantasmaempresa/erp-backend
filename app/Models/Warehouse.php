<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable
        = [
            'id',
            'name',
            'address',
            'type',
            'status',
        ];
    protected function setNameAttribute($value){
        $this->attributes['name'] = strtolower($value);
    }

    protected function getNameAttribute($value){
        return strtoupper($value);
    }

    protected function setAdressAttribute($value){
        $this->attributes['address'] = strtolower($value);
    }
    
    protected function getAdressAttribute($value){
        return strtoupper($value);
    }

    protected function setTypeAttribute($value){
        $this->attributes['type'] = strtolower($value);
    }
    
    protected function getTypeAttribute($value){
        return strtoupper($value);
    }

    protected function setStatusAttribute($value){
        $this->attributes['status'] = strtolower($value);
    }
    
    protected function getStatusAttribute($value){
        return strtoupper($value);
    }


    /**
     * Function to return array rules in method create and update
     *
     * @param $id
     *
     * @return array
     */
    public static function rules($id = null): array
    {
        $rule = [
            'name' => 'required|string',
            'address' => 'required|string',
            'type' => 'required|string',
            'status' => 'required|string',
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
        return $this->belongsToMany(Inventory::class);
    }
}

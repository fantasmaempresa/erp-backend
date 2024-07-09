<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovementTracking extends Model
{
    use HasFactory;

    protected $fillable
        = [
            'id',
            'article_id',
            'warehouse_id',
            'amount',
            'reason',
        ];

    protected function setReasonAttribute($value){
        $this->attributes['reason'] = strtolower($value);
    }

    protected function getReasonAttribute($value){
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
            'article_id' => 'required|int',
            'warehouse_id' => 'required|int',
            'amount' => 'required|int',
            'reason' => 'required|string|in:Inventario Inicial, Venta, Compra, Resguardo, Traslado Almacén'
        ];
        return $rule;
    }

}

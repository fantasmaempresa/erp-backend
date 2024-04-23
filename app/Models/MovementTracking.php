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
            'id_article',
            'id_warehouse',
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
            'id_article' => 'required|int',
            'id_warehouse' => 'required|int',
            'amount' => 'required|float',
            'reason' => 'required|string|in:Inventario Inicial, Venta, Compra, Resguardo, Traslado Almacén'
        ];
        return $rule;
    }

}

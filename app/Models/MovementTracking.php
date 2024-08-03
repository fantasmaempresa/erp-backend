<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovementTracking extends Model
{
    use HasFactory;

    const INITIAL_INVENTORY = 1;
    const SALE = 2;
    const PURCHASE = 3;
    const OFFICE_SECURITY_MEASURE = 4;
    const WAREHOUSE_TRANSFER = 5;

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
            'article_id' => 'required|exists:articles,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'amount' => 'required|int',
            'reason' => 'required|string|in:Inventario Inicial, Venta, Compra, Resguardo, Traslado Almacén'
        ];
        return $rule;
    }

    /**
     * @param $query
     * @param $search
     *
     * @return mixed
     */
    public function scopeSearch($query, $search): mixed
    {
        return $query->orWhere('article_id', 'like', "%$search%")
            ->orWhere('warehouse_id', 'like', "%$search%")
            ->orWhere('reason', 'like', "%$search%");
    }
}

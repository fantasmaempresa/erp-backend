<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable
        = [
            'id',
            'article_id',
            'warehouse_id',
            'amount',
        ];
        
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
        ];
        return $rule;
    }

    /**
     * @return HasMany
     */
    public function warehouse(): HasMany
    {
        return $this->HasMany(Warehouse::class);
    }
}

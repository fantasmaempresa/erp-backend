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
            'id_article',
            'id_warehouse',
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
            'id_article' => 'required|int',
            'id_warehouse' => 'required|int',
            'amount' => 'required|int',
        ];
        return $rule;
    }
}

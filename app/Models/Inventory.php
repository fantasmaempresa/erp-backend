<?php

/*
 * CODE
 * Inventory Model Class
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @access  public
 *
 * @version 1.0
 */
class Inventory extends Model
{
    /**
     * @var string[]
     */
    protected $fillable
        = [
            'id',
            'unit_quantity',
            'group_quantity',
            'item_id',
            'warehouse_id',
        ];

    /**
     * @return string[]
     */
    #[ArrayShape(['unit_quantity'  => "string",
                  'group_quantity' => "string",
                  'item_id'        => "string",
                  'warehouse_id'   => "string",
    ])] public static function rules(): array
    {
        return [
            'unit_quantity'  => 'integer',
            'group_quantity' => 'integer',
            'item_id'        => 'integer|required',
            'warehouse_id'   => 'integer|required',
        ];
    }

    /**
     * @return BelongsTo
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * @return BelongsTo
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}

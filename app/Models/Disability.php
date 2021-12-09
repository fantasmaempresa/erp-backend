<?php

/*
 * CODE
 * Disability Model Class
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
class Disability extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable
        = [
            'id',
            'days',
            'type',
            'discount',
            'date',
            'tax_datum_id',
        ];

    /**
     * @return string[]
     */
    #[ArrayShape(['days'         => "string",
                  'type'         => "string",
                  'discount'     => "string",
                  'date'         => "string",
                  'tax_datum_id' => "string",
    ])] public static function rules()
    {
        return [
            'days'         => 'required|string',
            'type'         => 'required|int',
            'discount'     => 'required|numeric',
            'date'         => 'required|date',
            'tax_datum_id' => 'required|int',
        ];
    }

    /**
     * @return BelongsTo
     */
    public function taxDatum(): BelongsTo
    {
        return $this->belongsTo(TaxDatum::class);
    }
}

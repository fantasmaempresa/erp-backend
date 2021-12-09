<?php

/*
 * CODE
 * ExtraHour Model Class
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
class ExtraHour extends Model
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
            'hours',
            'amount',
            'tax_datum_id',
        ];

    /**
     * @return string[]
     */
    #[ArrayShape(['days'         => "string",
                  'type'         => "string",
                  'hours'        => "string",
                  'amount'       => "string",
                  'tax_datum_id' => "string",
    ])] public static function rules(): array
    {
        return [
            'days'         => 'required|string',
            'type'         => 'required|int',
            'hours'        => 'required|string',
            'amount'       => 'required|numeric',
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

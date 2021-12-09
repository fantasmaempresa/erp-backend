<?php

/*
 * CODE
 * Deduction Model Class
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @access  public
 *
 * @version 1.0
 */
class Deduction extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable
        = [
            'id',
            'key',
            'type',
            'concept',
            'aggravated_amount',
            'exempt_amount',
        ];

    /**
     * @return string[]
     */
    #[ArrayShape(['key'               => "string",
                  'type'              => "string",
                  'concept'           => "string",
                  'aggravated_amount' => "string",
                  'exempt_amount'     => "string",
    ])] public static function rules(): array
    {
        return [
            'key'               => 'required|string',
            'type'              => 'int',
            'concept'           => 'required|string',
            'aggravated_amount' => 'required|numeric',
            'exempt_amount'     => 'required|numeric',
        ];
    }

    /**
     * @return BelongsToMany
     */
    public function taxData(): BelongsToMany
    {
        return $this->belongsToMany(TaxDatum::class);
    }
}

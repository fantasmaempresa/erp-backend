<?php

/*
 * CODE
 * Concept Model Class
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
class Concept extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'id',
        'name',
        'description',
        'formula',
        'amount',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'formula' => 'array',
    ];

    /**
     * @return string[]
     */
    #[ArrayShape(
        [
            'name' => "string",
            'description' => "string",
            'formula' => "string",
            'amount' => "string",
        ]
    )] public static function rules(): array
    {
        return [
            'name' => 'required|string',
            'description' => 'required|string',
            'formula' => 'nullable|array',
            'amount' => 'required|int',
        ];
    }

    /**
     * @return BelongsToMany
     */
    public function projectQuote(): BelongsToMany
    {
        return $this->belongsToMany(ProjectQuote::class);
    }
}

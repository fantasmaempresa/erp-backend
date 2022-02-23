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

    /**
     * @param $query
     * @param $search
     *
     * @return mixed
     */
    public function scopeSearch($query, $search): mixed
    {
        return $query->orWhere('name', 'like', "%$search%")
            ->orWhere('description', 'like', "%$search%")
            ->orWhere('formula', 'like', "%$search%")
            ->orWhere('amount', $search);
    }

    /**
     * @param array $formula
     * @param int   $valueConcept
     * @param int   $valueField
     *
     * @return int
     */
    public static function getOperation(array $formula, int $valueConcept, int $valueField): int
    {
        $total = 0;

        if ($formula['percentage']) {
            $total = round(($valueField * $formula['value']) / 100);
        } else {
            $total = $valueField;
        }

        return match (true) {
            $formula['operation'] === '+' => $total + $valueConcept,
            $formula['operation'] === '-' => $total - $valueConcept,
            $formula['operation'] === '*' => $total * $valueConcept,
            $formula['operation'] === '/' => $total / $valueConcept,
        };
    }
}

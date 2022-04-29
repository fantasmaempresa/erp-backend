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
            'formula' => 'required|array',
            'amount' => 'nullable|numeric',
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
     *
     * @return array|bool
     */
    public function verifyFormula(array $formula): array|bool
    {
        return match (true) {
            !isset($formula['percentage']) || !isset($formula['operable']) ||
            !isset($formula['validity']) || !isset($formula['range'])
            => ['error' => 'true', 'message' => 'field not found'],
            $formula['range']['apply'] && $formula['validity']['apply'] => ['error' => true, 'message' => '[formula[range][apply] and formula[validity][apply]] == true'],
            $formula['validity']['is_date'] && $formula['validity']['is_range'] => ['error' => true, 'message' => '[formula[validity][is_range] and formula[validity][is_date]] == true'],
            $formula['operable'] && $formula['validity']['apply'] => ['error' => true, 'message' => '[formula[operable] and formula[validity][apply]] == true'],
            $formula['operable'] && $formula['range']['apply'] => ['error' => true, 'message' => '[formula[operable] and formula[range][apply]] == true'],
            $formula['operable'] && empty($formula['operation']) => ['error' => true, 'message' => '[formula[operable] and empty formula[operation]] == true'],
            $formula['percentage'] && $formula['validity']['apply'] => ['error' => true, 'message' => '[formula[percentage] and [validity][apply]] == true'],
            $formula['percentage'] && $formula['range']['apply'] => ['error' => true, 'message' => '[formula[percentage] and formula[range][apply]] == true'],
            $formula['range']['apply'] => $this->verifyBetween($formula['range']['between']),
            $formula['validity']['apply'] && $formula['validity']['is_range'] => $this->verifyBetween($formula['validity']['between']),
            default => false
        };
    }

    /**
     * @param $between
     *
     * @return array|bool
     */
    public function verifyBetween($between): array|bool
    {
        foreach ($between as $item) {
            if (empty($item['min']) || empty($item['max']) || empty($item['amount'])) {
                return ['error' => 'true', 'message' => '[formulas[between] some empty data]'];
            }
        }

        return false;
    }


}

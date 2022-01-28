<?php

/*
 * CODE
 * StatusQuote Model Class
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @access  public
 *
 * @version 1.0
 */
class StatusQuote extends Model
{
    public static int $START = 1;
    public static int $REVIEW = 2;
    public static int $APPROVED = 3;
    public static int $FINISH = 4;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'id',
        'name',
        'description',
    ];

    /**
     * Function to return array rules in method create and update
     *
     * @return array
     */
    #[ArrayShape(
        [
            'name' => 'string',
            'description' => 'string',
        ]
    )] public static function rules(): array
    {
        return [
            'name' => 'required|string',
            'description' => 'required|string',
        ];
    }

    /**
     * @return HasMany
     */
    public function projectQuotes(): HasMany
    {
        return $this->hasMany(Project::class);
    }
}

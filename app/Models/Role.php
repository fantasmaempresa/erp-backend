<?php

/*
 * CODE
 * Role Model Class
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\Rule;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @access  public
 *
 * @version 1.0
 */
class Role extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'id',
        'name',
        'description',
        'config',
    ];
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'config' => 'array',
    ];

    /**
     * Function to return array rules in method create and update
     *
     * @return array
     */
    #[ArrayShape(
        [
            'name' => "string",
            'description' => "string",
            'config' => "string",
        ]
    )] public static function rules(): array
    {
        return [
            'name' => 'required|string',
            'description' => 'nullable|required|string',
            'config' => 'nullable|required|array',
        ];
    }

    /**
     * @return HasMany
     */
    public function user(): HasMany
    {
        return $this->hasMany(User::class);
    }
}


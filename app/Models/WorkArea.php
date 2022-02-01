<?php

/*
 * CODE
 * WorkArea Model Class
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
class WorkArea extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable
        = [
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
    protected $casts
        = [
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
            'description' => 'required|string',
            'config' => 'nullable|array',
        ];
    }

    /**
     * @return HasMany
     */
    public function staff(): HasMany
    {
        return $this->hasMany(Staff::class);
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
            ->orWhere('description', 'like', "%$search%");
    }
}


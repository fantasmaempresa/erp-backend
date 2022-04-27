<?php

/*
 * CODE
 * PhasesProcess Model Class
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Validation\Rule;

/**
 * @access  public
 *
 * @version 1.0
 */
class PhasesProcess extends Model
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
        'form',
        'quotes',
        'payments',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'form'     => 'array',
        'quotes'   => 'array',
        'payments' => 'array',
    ];
    /**
     * Function to return array rules in method create and update
     *
     * @return array
     */
    public static function rules(): array
    {
        return [
            'name' => 'required|string',
            'description' => 'nullable|string',
            'form' => 'required|array',
            'quotes' => 'nullable|array',
            'payments' => 'nullable|array',
        ];
    }

    /**
     * @return BelongsToMany
     */
    public function process(): BelongsToMany
    {
        return $this->belongsToMany(Process::class);
    }
}

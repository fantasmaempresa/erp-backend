<?php

/*
 * CODE
 * Projects Model Class
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @access  public
 *
 * @version 1.0
 */
class Project extends Model
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
        'estimate_end_date',
        'quotes',
        'folio',
        'user_id',
        'client_id',
    ];
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'quotes' => 'array',
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
            'estimate_end_date' => 'nullable|date',
            'quotes' => 'nullable|array',
            'folio' => 'nullable|string',
            'user_id' => 'required|int',
            'client_id' => 'nullable|int',
        ];
    }

    /**
     * @return BelongsTo
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsToMany
     */
    public function process(): BelongsToMany
    {
        return $this->belongsToMany(Process::class);
    }

    /**
     * @return BelongsToMany
     */
    public function staff(): BelongsToMany
    {
        return $this->belongsToMany(Staff::class);
    }
}

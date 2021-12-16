<?php

/*
 * CODE
 * ProjectQuote Model Class
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @access  public
 *
 * @version 1.0
 */
class ProjectQuote extends Model
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
        'date_end',
        'user_id',
        'project_id',
        'client_id',
        'status_quote_id',
    ];

    /**
     * @return string[]
     */
    #[ArrayShape(
        [
            'name' => "string",
            'description' => "string",
            'date_end' => "string",
            'project_id' => "string",
            'client_id' => "string",
            'status_quote_id' => "string",
        ]
    )] public static function rules(): array
    {
        return [
            'name' => 'required|string',
            'description' => 'required|string',
            'date_end' => 'required|date',
            'project_id' => 'nullable|int',
            'client_id' => 'nullable|int',
            'status_quote_id' => 'nullable|int',
        ];
    }

    /**
     * @return BelongsToMany
     */
    public function concept(): BelongsToMany
    {
        return $this->belongsToMany(Concept::class);
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->BelongsTo(User::class);
    }

    /**
     * @return BelongsTo
     */
    public function client(): BelongsTo
    {
        return $this->BelongsTo(Client::class);
    }

    /**
     * @return BelongsTo
     */
    public function project(): BelongsTo
    {
        return $this->BelongsTo(Project::class);
    }

    /**
     * @return BelongsTo
     */
    public function statusQuote(): BelongsTo
    {
        return $this->BelongsTo(StatusQuote::class);
    }
}

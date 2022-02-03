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
        'quote',
        'description',
        'observation',
        'addressee',
        'date_end',
        'user_id',
        'project_id',
        'client_id',
        'status_quote_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'quote' => 'array',
    ];

    /**
     * @return string[]
     */
    #[ArrayShape(
        [
            'name' => "string",
            'quote' => "array",
            'addressee' => "string",
            'description' => "string",
            'observation' => "string",
            'date_end' => "string",
            'project_id' => "string",
            'client_id' => "string",
            'status_quote_id' => "string",
        ]
    )] public static function rules(): array
    {
        return [
            'name' => 'required|string',
            'addressee' => 'required|string',
            'description' => 'required|string',
            'observation' => 'nullable|string',
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

    /**
     * @param int $statusNotify
     *
     * @return array
     */
    public static function getMessageNotify(int $statusNotify): array
    {
        $notifications = [
            StatusQuote::$START => [
                'message' => 'Nueva cotización creada',
                'type' => StatusQuote::$START,
            ],

            StatusQuote::$REVIEW => [
                'message' => 'La cotización fue puesta en estado de revisión',
                'type' => StatusQuote::$REVIEW,
            ],

            StatusQuote::$APPROVED => [
                'message' => '¡La cotización fue aprobada!',
                'type' => StatusQuote::$APPROVED,
            ],

            StatusQuote::$FINISH => [
                'message' => '¡La cotización fue finalizada!',
                'type' => StatusQuote::$FINISH,
            ],
        ];

        return $notifications[$statusNotify];
    }
}

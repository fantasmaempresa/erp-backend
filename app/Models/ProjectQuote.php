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
use Illuminate\Database\Eloquent\Relations\HasOne;
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
        'user_id',
        'project_id',
        'client_id',
        'status_quote_id',
        'template_quote_id',
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
            'project_id' => "int",
            'client_id' => "int",
            'status_quote_id' => "int",
            'template_quote_id' => "int",
        ]
    )] public static function rules(): array
    {
        return [
            'name' => 'required|string',
            'addressee' => 'required|string',
            'description' => 'required|string',
            'observation' => 'nullable|string',
            'project_id' => 'nullable|int',
            'client_id' => 'nullable|int',
            'status_quote_id' => 'nullable|int',
            'template_quote_id' => 'nullable|int',
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
     * @return HasOne
     */
    public function templateQuote(): HasOne
    {
        return $this->hasOne(TemplateQuotes::class);
    }

    /**
     * @param int    $statusNotify
     * @param string $name
     *
     * @return array
     */
    public static function getMessageNotify(int $statusNotify, string $name = ""): array
    {
        $notifications = [
            StatusQuote::$START => [
                'title' => 'Cotizaciones',
                'message' => "Nueva cotización creada ($name)",
                'type' => StatusQuote::$START,
            ],

            StatusQuote::$REVIEW => [
                'title' => 'Cotizaciones',
                'message' => "La cotización ($name) fue puesta en estado de revisión",
                'type' => StatusQuote::$REVIEW,
            ],

            StatusQuote::$APPROVED => [
                'title' => 'Cotizaciones',
                'message' => "¡La cotización ($name) fue aprobada!",
                'type' => StatusQuote::$APPROVED,
            ],

            StatusQuote::$FINISH => [
                'title' => 'Cotizaciones',
                'message' => "¡La cotización ($name) fue finalizada!",
                'type' => StatusQuote::$FINISH,
            ],
        ];

        return $notifications[$statusNotify];
    }
}

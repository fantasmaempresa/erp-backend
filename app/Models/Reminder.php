<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Reminder extends Model
{
    use HasFactory;

    #STATUS
    public static int $NO_NOTIFED = 0;
    public static int $NOTIFED = 1;
    public static int $EXPIRED = 2;
    public static int $DISABLE = 3;
    

    # CONFIG / TYPE
    public static int $PROCESSING_INCOME_CONFIG = 1;
    public static int $PROCEDURE_CONFIG = 2;
    public static int $GENERAL_CONFIG = 3;

    protected $fillable = [
        'id',
        'name',
        'message',
        'config',
        'status',
        'type',
        'expiration_date',
        'user_id',
        'relation_id',
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
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function rules(): array
    {
        $rule = [
            'name' => 'required|string',
            'type' => 'required|int|min:1|max:3',
            'expiration_date' => 'required|date',
            'config' => 'required|array',
            // 'relation_id' => 'nullable|int',
        ];

        return $rule;
    }

    public static function getConfig($config): array {
        
        $configs = [
            self::$PROCESSING_INCOME_CONFIG => [
                'processing_income_id' => 'required|exists:processing_incomes,id',
                'config.user_id' => 'nullable|exists:users,id',
            ],
            self::$PROCEDURE_CONFIG => [
                'procedure_id' => 'required|exists:procedures,id',
                'config.user_id' => 'nullable|exists:users,id',
            ],
            self::$GENERAL_CONFIG => [
                'config.role_id' => 'nullable|exists:roles,id',
            ],
        ];


        return $configs[$config] ?? [];
    }

}

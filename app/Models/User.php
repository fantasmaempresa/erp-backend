<?php

/*
 * CODE
 * User Model Class
 */

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Validation\Rule;
use JetBrains\PhpStorm\ArrayShape;
use Laravel\Passport\HasApiTokens;

/**
 * @access  public
 *
 * @version 1.0
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    public static int $ONLINE = 1;
    public static int $OFFLINE = 0;

    //ACTIONS
    public static int $LOGOUT = 1;
    public static int $LOCKED = 1;
    public static int $UNLOCKED = 0;

    /**
     * @var int
     */

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable
        = [
            'name',
            'email',
            'password',
            'role_id',
            'config',
            'online',
            'locked',
        ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden
        = [
            'password',
            'remember_token',
        ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'config' => 'array',
    ];

    /**
     * Function to return array rules in method create and update
     *
     * @param $id
     *
     * @return array
     */
    #[ArrayShape([
        'name' => "string",
        'email' => "array|string",
        'password' => "string|string[]",
        'role_id' => "string",
        'config' => "string",
    ])] public static function rules($id = null): array
    {
        $rule = [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'role_id' => 'required|int',
            'config' => 'nullable|required|array',
        ];

        if ($id) {
            // phpcs:ignore
            $rule['email'] = [
                'required', 'email',
                Rule::unique('users')->ignore($id)
            ];

            $rule['password'] = [
                'nullable', 'string', 'min:6',
            ];
        }

        return $rule;
    }

    /**
     * @return BelongsTo
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * @return HasOne
     */
    public function log(): HasOne
    {
        return $this->hasOne(UserLog::class);
    }

    /**
     * @return hasOne
     */
    public function client(): hasOne
    {
        return $this->hasOne(Client::class);
    }

    /**
     * @return hasOne
     */
    public function staff(): hasOne
    {
        return $this->hasOne(Staff::class);
    }

    /**
     * @return HasMany
     */
    public function project(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    /**
     * @return HasMany
     */
    public function notification(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * @return HasMany
     */
    public function AauthAcessToken(): HasMany
    {
        return $this->hasMany(OauthAccessToken::class);
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
            ->orWhere('email', 'like', "%$search%");
    }

    /**
     * @param int $statusNotify
     * @param string $name
     *
     * @return array
     */
    public static function getMessageNotify(int $statusNotify, string $name = ""): array
    {
        $notifications = [
            User::$ONLINE => [
                'title' => '¡Usuario conectado!',
                'message' => "El usuario ($name) acaba de iniciar sesion",
                'type' => User::$ONLINE,
            ],

            User::$OFFLINE => [
                'title' => '¡Usuario desconectado!',
                'message' => "El usuario ($name) acaba de cerrar sesion",
                'type' => User::$OFFLINE,
            ],
        ];

        return $notifications[$statusNotify];
    }

    /**
     * @param int $action
     *
     * @return array
     */
    public static function getActionSystem(int $action): array
    {
        return [
            User::$LOGOUT => [
                'action' => 'logout',
                'remove_session' => 'true',
            ],
        ];
    }
}

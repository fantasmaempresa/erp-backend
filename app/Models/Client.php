<?php

/*
 * CODE
 * Clients Model Class
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\Rule;

/**
 * @access  public
 *
 * @version 1.0
 */
class Client extends Model
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
            'email',
            'phone',
            'nickname',
            'address',
            'rfc',
            'extra_information',
            'user_id',
        ];

    protected $casts
        = [
            'extra_information' => 'array',
        ];

    /**
     * Function to return array rules in method create and update
     *
     * @param $id
     *
     * @return array
     */
    public static function rules($id = null): array
    {
        $rule = [
            'name' => 'required|string',
            'email' => 'required|email|unique:clients',
            'phone' => 'required|string|max:10|min:10|unique:clients',
            'nickname' => 'nullable|string',
            'address' => 'nullable|string',
            'rfc' => 'nullable|required|string|max:13|min:10|unique:clients',
            'extra_information' => 'nullable|array',
            'user_id'           => 'nullable|int',
        ];

        if ($id) {
            // phpcs:ignore
            $rule['email'] = [
                'required',
                'email',
                Rule::unique('clients')->ignore($id),
            ];
            // phpcs:ignore
            $rule['phone'] = [
                'string',
                'max:10',
                'min:10',
                Rule::unique('clients')->ignore($id),
            ];
            // phpcs:ignore
            $rule['rfc'] = [
                'string',
                'max:13',
                'min:10',
                Rule::unique('clients')->ignore($id),
            ];
        }

        return $rule;
    }

    /**
     * @return belongsTo
     */
    public function user(): belongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany
     */
    public function project(): HasMany
    {
        return $this->HasMany(Project::class);
    }

    /**
     * @return BelongsToMany
     */
    public function clientDocument(): BelongsToMany
    {
        return $this->belongsToMany(Document::class);
    }
}

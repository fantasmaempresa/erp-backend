<?php

/*
 * CODE
 * Client Link Model Class
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
class ClientLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'email',
        'phone',
        'nickname',
        'address',
        'rfc',
        'profession',
        'degree',
        'user_id',
        'client_id',
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
            'email' => 'required|email|unique:client_links',
            'phone' => 'required|string|max:10|min:10|unique:client_links',
            'nickname' => 'nullable|string',
            'address' => 'nullable|string',
            'profession' => 'nullable|string',
            'degree' => 'nullable|string',
            'rfc' => 'nullable|required|string|max:13|min:10|unique:client_links',
            'user_id' => 'nullable|int',
            'client_id' => 'required|int',
        ];

        if ($id) {
            // phpcs:ignore
            $rule['email'] = [
                'required',
                'email',
                Rule::unique('client_links')->ignore($id),
            ];
            // phpcs:ignore
            $rule['phone'] = [
                'string',
                'max:10',
                'min:10',
                Rule::unique('client_links')->ignore($id),
            ];
            // phpcs:ignore
            $rule['rfc'] = [
                'string',
                'max:13',
                'min:10',
                Rule::unique('client_links')->ignore($id),
            ];
        }

        return $rule;
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * @return BelongsToMany
     */
    public function documents(): BelongsToMany
    {
        return $this->belongsToMany(Document::class);
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
            ->orWhere('phone', 'like', "%$search%")
            ->orWhere('nickname', 'like', "%$search%")
            ->orWhere('address', 'like', "%$search%")
            ->orWhere('rfc', 'like', "%$search%");
    }
}

<?php

/*
 * CODE
 * Staff Model Class
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Validation\Rule;
use JetBrains\PhpStorm\ArrayShape;
use function PHPUnit\Framework\isNull;

/**
 * @access  public
 *
 * @version 1.0
 */
class Staff extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'id',
        'name',
        'email',
        'phone',
        'nickname',
        'extra_information',
        'work_area_id',
        'user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
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
            'email' => 'required|email|unique:staff',
            'phone' => 'string|max:10|min:10|unique:staff',
            'nickname' => 'nullable|string',
            'extra_information' => 'nullable',
            'work_area_id' => 'int',
            'user_id' => 'nullable|int',
        ];

        if ($id) {
            // phpcs:ignore
            $rule['email'] = [
                'required', 'email',
                Rule::unique('staff')->ignore($id)
            ];
            // phpcs:ignore
            $rule['phone'] = [
                'string', 'max:10', 'min:10',
                Rule::unique('staff')->ignore($id)
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
    public function workArea(): BelongsTo
    {
        return $this->belongsTo(WorkArea::class);
    }

    /**
     * @return BelongsToMany
     */
    public function project(): BelongsToMany
    {
        return $this->belongsToMany(Project::class);
    }
}

<?php

/**
 * OPEN2CODE
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use phpseclib3\Crypt\EC\Curves\brainpoolP160r1;

/**
 * Model shape
 */
class Shape extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'form',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'form' => 'array'
    ];

    /**
     * @return HasMany
     */
    public function procedures(): HasMany
    {
        return $this->hasMany(Procedure::class);
    }

    /**
     * @param $query
     * @param $search
     *
     * @return mixed
     */
    public function scopeSearch($query, $search): mixed
    {
        return $query->orWhere('name', 'like', "%$search%");
    }

    /**
     * @return string[]
     */
    public static function rules()
    {
        return [
            'name' => 'required|string',
            'form' => 'required|array',
        ];
    }

    /**
     * @return bool
     */
    public function verifyForm(): bool
    {
        $form = $this->getAttribute('form');

        $types = [
            'text',
            'number',
            'date',
        ];

        $keys = [
            'name',
            'type',
            'label',
        ];

        $flag = true;
        foreach ($form as $field) {
            if (!empty(array_diff($keys, array_keys($field)))) {
                $flag = false;
                break;
            }

            if (!in_array($field['type'], $types)) {
                $flag = false;
                break;
            }
        }

        return $flag;
    }


}

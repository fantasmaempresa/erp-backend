<?php

/*
 * OPEN 2 CODE TEMPLATE SHAPE MODEL
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @version
 */
class TemplateShape extends Model
{
    use HasFactory;

    const FORM01 = 1;
    const FORM02 = 2;
    const FORM03 = 3;
    const FORM04 = 4;

    protected $fillable = [
        'id',
        'name',
        'file',
        'form',
    ];

    protected function setNameAttribute($value){
        $this->attributes['name'] = strtolower($value);
    }
    
    protected function getNameAttribute($value){
        return strtoupper($value);
    }


    /**
     * @var string[]
     */
    protected $casts = [
        'form' => 'array'
    ];

    /**
     * @return HasMany
     */
    public function shapes(): HasMany
    {
        return $this->hasMany(Shape::class);
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
            'file' => 'nullable|file',
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

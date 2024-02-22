<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IsoDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'rule',
        'description',
        'file',
    ];


     /**
     * @param $query
     * @param $search
     *
     * @return mixed
     */
    public function scopeSearch($query, $search): mixed
    {
        return $query
            ->orWhere('name', 'like', "%$search%")
            ->orWhere('rule', 'like', "%$search%")
            ->orWhere('description', 'like', "%$search%");
    }

     /**
     * @return string[]
     */
    public static function rules(): array
    {
        return [
            'name' => 'required|string',
            'rule' => 'required|string',
            'description' => 'required|string',
        ];
    }
}

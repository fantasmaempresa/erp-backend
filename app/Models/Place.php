<?php
/**
 * Open2Code
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Place model first version
 */
class Place extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = ['name'];

    /**
     * @return HasMany
     */
    public function procedure(): HasMany
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
        return $query
            ->orWhere('name', 'like', "%$search%");
    }

    /**
     * @return string[]
     */
    public static function rules(): array
    {
        return [
            'name' => 'required|string'
        ];
    }
}

<?php
/**
 * open2code
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * first versión
 */
class Grantor extends Model
{
    use HasFactory;

    const MORAL_PERSON = 1;
    const PHYSICAL_PERSON = 2;

    const BENEFICIARY = true;
    const NO_BENEFICIARY = false;

    /**
     * @return BelongsToMany
     */
    public function procedures(): BelongsToMany
    {
        return $this->belongsToMany(Procedure::class);
    }

    protected $fillable = [
        'id',
        'name',
        'father_last_name',
        'mother_last_name',
        'type',
        'stake',
        'beneficiary',
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
            ->orWhere('father_last_name', 'like', "%$search%")
            ->orWhere('mother_last_name', 'like', "%$search%")
            ->orWhere('beneficiary', 'like', "%$search%")
            ->orWhere('stake', 'like', "%$search%");
    }

    /**
     * @return string[]
     */
    public static function rules(): array
    {
        return [
            'name' => 'required|string',
            'father_last_name' => 'nullable|string',
            'mother_last_name' => 'nullable|string',
            'type' => 'nullable|int',
            'stake' => 'required|string',
            'beneficiary' => 'required|boolean',
        ];
    }
}

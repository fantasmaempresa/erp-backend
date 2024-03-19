<?php
/*
 * OPEN 2 CODE PROCEDURE COMMENT MODEL
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @version
 */
class ProcedureComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'comment',
        'procedure_id',
        'user_id',
    ];

    /**
     * @return BelongsTo
     */
    public function procedure(): BelongsTo
    {
        return $this->belongsTo(Procedure::class);
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return string[]
     */
    public static function rules(): array
    {
        return [
            'comment' => 'required',
            'procedure_id' => 'required',
        ];
    }

    /**
     * @param $query
     * @param $search
     *
     * @return mixed
     */
    public function scopeSearch($query, $search, $procedure_id): mixed
    {
        return $query
            ->where('procedure_id', $procedure_id)
            ->orWhere('comment', 'like', "%$search");
    }
}

<?php
/*
 * OPEN 2 CODE PROCEDURE COMMENT MODEL
 */

namespace App\Models;

use App\Traits\NotificationTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Events\NotificationEvent;

/**
 * @version
 */
class ProcedureComment extends Model
{
    use HasFactory, NotificationTrait;


    protected $fillable = [
        'id',
        'comment',
        'procedure_id',
        'user_id',
    ];

    protected function setCommentAttribute($value)
    {
        $this->attributes['comment'] = strtolower($value);
    }

    protected function getCommentAttribute($value)
    {
        return strtoupper($value);
    }

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

    public function notify()
    {
        $procedure = Procedure::find($this->procedure_id);
        $notification = $this->createNotification([
            "title" => "Se ha registrado un nuevo comentario",
            "message" => "Se ha registrado un nuevo comentario para el expediente : ($procedure->name)"
        ], null, Role::$ADMIN);
        $this->sendNotification(
            $notification,
            null,
            new NotificationEvent($notification, 0, Role::$ADMIN, [])
        );
    }
}

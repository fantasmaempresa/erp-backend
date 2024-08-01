<?php

namespace App\Models;

use App\Traits\NotificationTrait;
use Illuminate\Database\Eloquent\Model;
use App\Events\NotificationEvent;

class ProcessingIncomeComment extends Model
{
    use NotificationTrait;

    protected $fillable = [
        'id',
        'comment',
        'user_id',
        'processing_income_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function processingIncome()
    {
        return $this->belongsTo(ProcessingIncome::class);
    }

    /**
     * @param $query
     * @param $search
     *
     * @return mixed
     */
    public function scopeSearch($query, $search, $processing_income_id): mixed
    {
        return $query
            ->where('processing_income_id', $processing_income_id)
            ->orWhere('comment', 'like', "%$search");
    }

    public static function rules()
    {
        return [
            'comment' => 'required',
            'processing_income_id' => 'required',
        ];
    }

    public function notify()
    {
        $procesingIncome = ProcessingIncome::find($this->processing_income_id);
        $notification = $this->createNotification([
            "title" => "Se ha registrado un nuevo comentario",
            "message" => "Se ha registrado un nuevo comentario para el expediente : ($procesingIncome->name)",
        ], null, Role::$ADMIN);

        $this->sendNotification($notification, null, new NotificationEvent($notification, 0, Role::$ADMIN, []));
    }
}

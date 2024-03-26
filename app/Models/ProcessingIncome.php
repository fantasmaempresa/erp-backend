<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcessingIncome extends Model
{
    const DOCUMENT_REGISTER = 1;
    const DOCUMENT_OUTPUT = 2;
    const DOCUMENT_RETURN = 3;

    protected $fillable = [
      'id',
      'name',
      'date_income',
      'config',
      'procedure_id',
      'operation_id',
      'staff_id',
      'place_id',
      'user_id',  
    ];

    protected $casts = [
        'config' => 'array'
    ];

    public function procedure(){
        return $this->belongsTo(Procedure::class);
    }

    public function operation(){
        return $this->belongsTo(Operation::class);
    }

    public function staff(){
        return $this->belongsTo(Staff::class);
    }

    public function place(){
        return $this->belongsTo(Place::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function documents(){
        return $this->belongsToMany(Document::class)->withPivot(['file', 'type']);
    }

    public function processingIncomeComments(){
        return $this->hasMany(ProcessingIncomeComment::class);
    }

    public static function getMessageNotify(int $statusNotify, string $name = ''){
        $notifications = [
          self::DOCUMENT_REGISTER => [
            'title' => 'Se ha registrado un nuevo documento de registro',
            'message' => "Se ha registrado un nuevo documento de registro para el expediente : ($name)",
            'type' => self::DOCUMENT_REGISTER,
          ],
          self::DOCUMENT_OUTPUT => [
            'title' => 'Se ha registrado un nuevo documento de salida',
            'message' => "Se ha registrado un nuevo documento de salida para el expediente : ($name)",
            'type' => self::DOCUMENT_OUTPUT,
          ],
          self::DOCUMENT_RETURN => [
            'title' => 'Se ha registrado un nuevo documento que regresa',
            'message' => "Se ha registrado un nuevo documento que regresa para el expediente : ($name)",
            'type' => self::DOCUMENT_RETURN,
          ]
        ];

        return $notifications[$statusNotify];
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
            ->orWhere('name', 'like', "%$search")
            ->orWhere('date_income', 'like', "%$search");
    }

    public static function rules(){
        return [
            'name' => 'required|string',
            'date_income' => 'required|date',
            'config' => 'nullable|array',
            'procedure_id' => 'required|exists:procedures,id',
            'operation_id' => 'required|exists:operations,id',
            'staff_id' => 'required|exists:staff,id',
            'place_id' => 'required|exists:places,id',
        ];
    }
}

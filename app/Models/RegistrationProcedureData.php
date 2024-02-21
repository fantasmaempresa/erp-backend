<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistrationProcedureData extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'id',
        'inscription',
        'sheets',
        'took',
        'date',
        'property',
        'url_file',
        'procedure_id',
        'document_id',
        'user_id',
    ];

    /**
     * @return BelongsToMany
     */
    public function procedure(){
        return $this->belongsTo(Procedure::class);
    }

    /**
     * @return BelongsToMany
     */
    public function document(){
        return $this->belongsTo(Document::class);
    }

    /**
     * @return BelongsToMany
     */
    public function user(){
        return $this->belongsTo(User::class);
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
            ->orWhere('inscription', 'like', "%$search%")
            ->orWhere('sheets', 'like', "%$search%")
            ->orWhere('took', 'like', "%$search%")
            ->orWhere('date', 'like', "%$search%")
            ->orWhere('property', 'like', "%$search%");
    }

     /**
     * @return string[]
     */
    public static function rules(): array
    {
        return [
            'inscription' => 'required|string',
            'sheets' => 'nullable|string',
            'took' => 'nullable|string',
            'date' => 'nullable|date',
            'property' => 'nullable|string',
            'procedure_id' => 'required|exists:procedures,id',
            'document_id' => 'required|exists:documents,id',
        ];
    }
}

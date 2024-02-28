<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistrationProcedureData extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'date',
        'inscription',
        'sheets',
        'took',
        'book',
        'departure',
        'folio_real_estate',
        'folio_electronic_merchant',
        'nci',
        'description',
        'url_file',
        'document_id',
        'procedure_id',
        'place_id',
        'user_id',
    ];

    /**
     * @return BelongsTo
     */
    public function procedure()
    {
        return $this->belongsTo(Procedure::class);
    }

    /**
     * @return BelongsTo
     */
    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * @return BelongsTo
     */
    public function user()
    {
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
            ->orWhere('book', 'like', "%$search%")
            ->orWhere('departure', 'like', "%$search%")
            ->orWhere('folio_real_estate', 'like', "%$search%")
            ->orWhere('folio_electronic_merchant', 'like', "%$search%")
            ->orWhere('nci', 'like', "%$search%");
    }

    /**
     * @return string[]
     */
    public static function rules(): array
    {
        return [
            'date' => 'required|date',
            'inscription' => 'nullable|string',
            'sheets' => 'nullable|string',
            'took' => 'nullable|string',
            'book' => 'nullable|string',
            'departure' => 'nullable|string',
            'folio_real_estate' => 'nullable|string',
            'folio_electronic_merchant' => 'nullable|string',
            'description' => 'nullable|string',
            'nci' => 'nullable|string',
            'procedure_id' => 'required|exists:procedures,id',
            'place_id' => 'required|exists:procedures,id',
            'document_id' => 'nullable|exists:documents,id',
        ];
    }
}

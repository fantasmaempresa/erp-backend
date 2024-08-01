<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentProcedure extends Model
{
    use HasFactory;

      /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'id',
        'procedure_id',
        'document_id',
        'file',
    ];

    protected $table = "document_procedure";

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
   public function document(): BelongsTo
   {
       return $this->belongsTo(Document::class);
   }
}

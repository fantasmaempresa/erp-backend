<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentProcessingIncome extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
    */
    protected $fillable = [ 
        'id',
        'processing_income_id',
        'document_id',
        'file',
        'type',
    ];

    protected $table = "document_processing_income";

    /**
    * @return BelongsTo
    */
   public function processing_income(): BelongsTo
   {
       return $this->belongsTo(ProcessingIncome::class);
   }

   /**
    * @return BelongsTo
    */
   public function document(): BelongsTo
   {
       return $this->belongsTo(Document::class);
   }
}

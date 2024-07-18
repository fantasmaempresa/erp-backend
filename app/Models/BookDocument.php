<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookDocument extends Model
{
    use HasFactory;

      /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'id',
        'book_id',
        'document_id',
        'file',
    ];

    protected $table = "book_document";

    /**
    * @return BelongsTo
    */
   public function book(): BelongsTo
   {
       return $this->belongsTo(Book::class);
   }

   /**
    * @return BelongsTo
    */
   public function document(): BelongsTo
   {
       return $this->belongsTo(Document::class);
   }
}

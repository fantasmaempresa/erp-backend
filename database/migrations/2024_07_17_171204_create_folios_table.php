<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFoliosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('folios', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('name')->unique(); //este campo es para el instrumento y debe de ser único y continuo
            $table->bigInteger('folio_min')->nullable(); //rango bajo de folio
            $table->bigInteger('folio_max'); //rango alto de folio
            $table->json('unused_folios')->nullable(); //folios inutilizados
            $table->foreignId('book_id')->constrained(); //libro
            $table->foreignId('procedure_id')->nullable()->constrained(); //procedimiento
            $table->foreignId('user_id')->constrained(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('folios');
    }
}

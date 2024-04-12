<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentProcessingIncomeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document_processing_income', function (Blueprint $table) {
            //TODO eliminar después de ejcutar en el server ALTER TABLE document_processing_income ADD id INT AUTO_INCREMENT PRIMARY KEY NOT NULL;
            $table->id();
            $table->foreignId('document_id')->constrained();
            $table->foreignId('processing_income_id')->constrained();
            $table->string('file')->nullable();
            $table->tinyInteger('type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('document_processing_income');
    }
}

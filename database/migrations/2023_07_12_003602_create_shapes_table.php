<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShapesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shapes', function (Blueprint $table) {
            $table->id();
            $table->string('folio'); //folio
            $table->string('notary'); //notario --- este podría ser el id del staff 1 u 2
            $table->string('scriptures'); //Escritura
            $table->string('property_account')->nullable(); //Cuenta Predial
            $table->date('signature_date'); //Fecha Firma
            $table->string('departure')->nullable(); //Partida
            $table->string('inscription')->nullable(); //Inscripción
            $table->string('sheets')->nullable(); //Fojas
            $table->string('took')->nullable(); //Tomo
            $table->string('book')->nullable(); //Libro
            $table->string('operation_value')->nullable(); //ValorOperacion
            $table->text('description'); //Descripcion
            $table->string('total'); //Total
            $table->json('data_form'); //Tipo de de trámite
            $table->text('reverse')->nullable(); //Reverso

            $table->foreignId('template_shape_id')->constrained(); //Tipo de de trámite
            $table->foreignId('procedure_id')->constrained(); // trámite al que pertenece esta forma
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
        Schema::dropIfExists('shapes');
    }
}

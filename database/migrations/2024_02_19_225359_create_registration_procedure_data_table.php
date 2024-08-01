<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegistrationProcedureDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('registration_procedure_data', function (Blueprint $table) {
            $table->id();
            $table->date('date'); // fecha de inscripción
            $table->string('inscription')->nullable(); //inscripción
            $table->string('sheets')->nullable(); //fojas
            $table->string('took')->nullable(); //tomo
            $table->string('book')->nullable(); //libro
            $table->string('departure')->nullable(); //partida
            $table->string('folio_real_estate')->nullable();//  folio real inmobiliario
            $table->string('folio_electronic_merchant')->nullable();// folio mercantil electrónico
            $table->string('nci')->nullable();// NCI
            $table->string('url_file')->nullable();//  archivo
            $table->text('description')->nullable();//  descripción
            $table->foreignId('document_id')->nullable()->constrained(); // Documento
            $table->foreignId('procedure_id')->constrained(); // Trámite
            $table->foreignId('place_id')->constrained(); // lugar
            $table->foreignId('user_id')->constrained(); // Documento

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
        Schema::dropIfExists('registration_procedure_data');
    }
}

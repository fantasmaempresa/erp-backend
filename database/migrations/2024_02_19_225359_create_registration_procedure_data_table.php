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
            $table->string('inscription');
            $table->string('sheets')->nullable();
            $table->string('took')->nullable();
            $table->date('date');
            $table->string('property')->nullable();
            $table->string('url_file');
            $table->foreignId('procedure_id')->constrained(); // Trámite
            $table->foreignId('document_id')->constrained(); // Documento
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

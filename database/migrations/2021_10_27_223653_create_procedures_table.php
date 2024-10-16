<?php

use App\Models\Procedure;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProceduresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('procedures', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); //Expediente
            $table->string('value_operation')->nullable(); // Valor de Operación
            $table->date('date'); //fecha
            $table->date('date_proceedings')->nullable(); //fecha
            $table->string('credit')->nullable(); // credito
            $table->text('observation')->nullable(); // observaciones
            $table->tinyInteger('status')->default(Procedure::IN_PROCESS);
            $table->string('appraisal')->nullable(); // avluo
            $table->date('date_appraisal')->nullable(); //fecha de avluo
            $table->foreignId('user_id')->constrained(); //Elaboro
            $table->foreignId('place_id')->constrained(); //Lugar
            $table->foreignId('client_id')->constrained(); //Cliente
            $table->foreignId('staff_id')->constrained(); //Gestor / responable

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
        Schema::dropIfExists('procedures');
    }
}

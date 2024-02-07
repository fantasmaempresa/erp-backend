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
            $table->string('value_operation'); // Valor de Operación
            $table->date('date_proceedings'); //Fecha
            $table->string('instrument'); //instrumento
            $table->date('date'); //fecha
            $table->string('volume'); // volumen
            $table->bigInteger('folio_min')->nullable(); //rango bajo de folio
            $table->bigInteger('folio_max'); //rango alto de folio
            $table->string('credit')->nullable(); // credito
            $table->text('observation'); // observaciones
            $table->tinyInteger('status')->default(Procedure::IN_PROCESS);
            $table->foreignId('operation_id')->constrained(); //Operacion
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

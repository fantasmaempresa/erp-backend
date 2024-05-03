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
            $table->string('instrument'); //instrumento
            $table->date('date'); //fecha
            $table->date('date_proceedings')->nullable(); //fecha
            $table->string('volume'); // volumen
            $table->bigInteger('folio_min')->nullable(); //rango bajo de folio
            $table->bigInteger('folio_max'); //rango alto de folio
            $table->string('credit')->nullable(); // credito
            $table->text('observation')->nullable(); // observaciones
            $table->tinyInteger('status')->default(Procedure::IN_PROCESS);
            $table->tinyInteger('way_to_pay')->nullable();
            $table->string('real_estate_folio')->nullable();
            $table->string('meters_land')->nullable();
            $table->string('construction_meters')->nullable();
            $table->tinyInteger('property_type')->nullable();
            $table->string('appraisal')->nullable();
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

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
            $table->string('property_account'); //Cuenta Predial
            $table->date('signature_date'); //Fecha Firma
            $table->string('departure'); //Partida
            $table->string('inscription'); //Inscripción
            $table->string('sheets');//Fojas
            $table->string('took'); //Tomo
            $table->string('book'); //Libro
            $table->string('operation_value'); //ValorOperacion
            $table->string('alienating_name'); //Nombre Enajenante
            $table->string('alienating_street'); //Calle Enajenante
            $table->string('alienating_outdoor_number'); //NoExt Enajenante
            $table->string('alienating_interior_number'); //NoInt Enajenante
            $table->string('alienating_colony'); //Colonia Enajenante
            $table->string('alienating_locality'); //Localidad Enajenante
            $table->string('alienating_municipality'); //MunicipioEnajenante
            $table->string('alienating_entity'); //Entidad Enajenante
            $table->string('alienating_zipcode'); //CodPos Enajenante
            $table->string('alienating_phone'); //Telefono Enajenante
            $table->string('acquirer_name'); //Nombre Adquiriente
            $table->text('description'); //Descripcion
            $table->string('total'); //Total
            $table->json('data_form'); //Tipo de de trámite

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
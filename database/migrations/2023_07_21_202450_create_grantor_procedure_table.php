<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGrantorProcedureTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grantor_procedure', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grantor_id')->constrained();
            $table->foreignId('procedure_id')->constrained();
            $table->foreignId('stake_id')->constrained();
            $table->decimal('percentage', 15, 4)->nullable();
            $table->decimal('amount', 15, 4)->nullable();
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
        Schema::dropIfExists('grantor_procedure');
    }
}

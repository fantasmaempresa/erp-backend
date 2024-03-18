<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProcessingIncomesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('processing_incomes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('date_income');
            $table->json('config');
            $table->string('url_file');
            $table->foreignId('procedure_id')->constrained();
            $table->foreignId('operation_id')->constrained();
            $table->foreignId('staff_id')->constrained();
            $table->foreignId('place_id')->constrained();
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
        Schema::dropIfExists('processing_incomes');
    }
}

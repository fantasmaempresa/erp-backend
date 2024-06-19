<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVulnerableOperationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vulnerable_operations', function (Blueprint $table) {
            $table->id();
            $table->string('type_category')->nullable();
            $table->string('type_vulnerable_operation')->nullable();
            $table->json('grantor_first_id')->nullable();
            $table->json('grantor_second_id')->nullable();
            $table->json('vulnerable_operation_data')->nullable();
            $table->foreignId('procedure_id')->constrained();
            $table->foreignId('unit_id')->nullable()->constrained();
            $table->foreignId('inversion_unit_id')->nullable()->constrained();
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
        Schema::dropIfExists('vulnerable_operations');
    }
}

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
            $table->json('data_form');
            $table->string('capital')->nullable();
            $table->string('constitution')->nullable();
            $table->string('increase')->nullable();
            $table->string('capital_decrease')->nullable();
            $table->string('sale_shares')->nullable();
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

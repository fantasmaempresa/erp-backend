<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->boolean('billable')->default(false);
            $table->string('bar_code');
            $table->string('name');
            $table->string('image');
            $table->foreignId('line_id')->constrained();
            $table->float('purchase_cost')->nullable();
            $table->float('sale_cost')->nullable();
            $table->string('type');
            $table->string('brand')->nullable();
            $table->boolean('storable');
            $table->string('purchase_measure_unit');
            $table->string('sale_measure_unit');
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
        Schema::dropIfExists('articles');
    }
}

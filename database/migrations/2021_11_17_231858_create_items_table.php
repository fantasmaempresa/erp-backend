<?php

/*
 * CODE
 * Item Class Migration
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @access  public
 *
 * @version 1.0
 */
class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->boolean('billable')->default(false);
            $table->string('code')->unique();
            $table->string('description');
            $table->string('image')->nullable();
            $table->string('line')->nullable();
            $table->decimal('purchase_amount')->nullable();
            $table->decimal('sale_amount')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->boolean('storable')->default(false);
            $table->string('trademark')->nullable();
            $table->string('unit_measure_sale')->nullable();
            $table->string('unit_measure_purchase')->nullable();
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
        Schema::dropIfExists('items');
    }
}

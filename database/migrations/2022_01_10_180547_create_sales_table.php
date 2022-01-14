<?php

/*
 * CODE
 * Sales Class Migration
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @access  public
 *
 * @version 1.0
 */
class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->decimal('discount', 15, 4);
            $table->string('folio')->unique();
            $table->string('series');
            $table->tinyInteger('status');
            $table->decimal('total', 15, 4);
            $table->decimal('subtotal', 15, 4);
            $table->foreignId('warehouse_id')->constrained();
            $table->foreignId('client_id')->constrained();
            $table->foreignId('staff_id')->constrained();
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
        Schema::dropIfExists('sales');
    }
}

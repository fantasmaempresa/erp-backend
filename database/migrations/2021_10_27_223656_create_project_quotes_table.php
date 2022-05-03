<?php

/*
 * CODE
 * ProjectQuote Class Migration
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @access  public
 *
 * @version 1.0
 */
class CreateProjectQuotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_quotes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->text('observation')->nullable();
            $table->text('addressee');
            $table->json('quote')->nullable();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('client_id')->nullable()->constrained();
            $table->foreignId('status_quote_id')->nullable()->constrained();
            $table->foreignId('template_quote_id')->nullable()->constrained();
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
        Schema::dropIfExists('project_quotes');
    }
}

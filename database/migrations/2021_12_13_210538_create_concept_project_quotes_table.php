<?php

/*
 * CODE
 * ConceptProjectQuote Class Migration
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @access  public
 *
 * @version 1.0
 */
class CreateConceptProjectQuotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('concept_project_quote', function (Blueprint $table) {
            $table->foreignId('project_quote_id')->constrained();
            $table->foreignId('concept_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('concept_project_quote');
    }
}

<?php

/*
 * CODE
 * Projects Class Migration
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @access  public
 *
 * @version 1.0
 */
class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->date('estimate_end_date')->nullable();
            $table->string('folio')->nullable();
            $table->json('config');
            $table->boolean('finished')->default(\App\Models\Project::$UNFINISHED);
            $table->foreignId('procedure_id')->constrained(); //trámite
            $table->foreignId('user_id')->constrained(); //Usuario quien inicio el proyecto
            $table->foreignId('client_id')->nullable()->constrained();
            $table->foreignId('project_quote_id')->nullable()->constrained();
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
        Schema::dropIfExists('projects');
    }
}

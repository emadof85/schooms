<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOptionLabelsToFieldDefinitionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('field_definitions', function (Blueprint $table) {
            $table->json('option_labels')->nullable(); // Store translated option labels as JSON
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('field_definitions', function (Blueprint $table) {
            $table->dropColumn('option_labels');
        });
    }
}

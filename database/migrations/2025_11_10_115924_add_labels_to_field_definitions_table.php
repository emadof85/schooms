<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLabelsToFieldDefinitionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('field_definitions', function (Blueprint $table) {
            $table->json('labels')->nullable()->after('label'); // Store labels in multiple languages
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
            $table->dropColumn('labels');
        });
    }
}

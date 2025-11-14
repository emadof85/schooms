<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeStateLgaToTextInUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['state_id']);
            $table->dropForeign(['lga_id']);
            $table->dropColumn(['state_id', 'lga_id']);
            $table->string('state')->nullable();
            $table->string('lga')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['state', 'lga']);
            $table->unsignedInteger('state_id')->nullable();
            $table->unsignedInteger('lga_id')->nullable();
            $table->foreign('state_id')->references('id')->on('states')->onDelete('set null');
            $table->foreign('lga_id')->references('id')->on('lgas')->onDelete('set null');
        });
    }
}

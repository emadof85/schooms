<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFieldValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('field_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('field_definition_id');
            $table->unsignedBigInteger('entity_id'); // student_record_id or user_id
            $table->string('entity_type')->default('student_record'); // To extend to other entities
            $table->text('value')->nullable(); // Store the actual value
            $table->timestamps();

            $table->foreign('field_definition_id')->references('id')->on('field_definitions')->onDelete('cascade');
            $table->index(['entity_id', 'entity_type']);
            $table->unique(['field_definition_id', 'entity_id', 'entity_type']); // One value per field per entity
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('field_values');
    }
}

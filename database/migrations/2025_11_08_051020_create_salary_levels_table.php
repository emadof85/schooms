<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalaryLevelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // create_salary_levels_table
        Schema::create('salary_levels', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name'); // e.g., "Level 1", "Senior Teacher", "Principal"
            $table->unsignedInteger('user_type_id'); // Links to user_types table
            $table->decimal('base_salary', 10, 2);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->foreign('user_type_id')->references('id')->on('user_types')->onDelete('cascade'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('salary_levels');
    }
}

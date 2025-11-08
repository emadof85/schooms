<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalaryStructuresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // create_salary_structures_table
        Schema::create('salary_structures', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('salary_level_id');
            $table->unsignedInteger('user_id');
            $table->decimal('basic_salary', 10, 2);
            $table->decimal('housing_allowance', 10, 2)->default(0);
            $table->decimal('transport_allowance', 10, 2)->default(0);
            $table->decimal('medical_allowance', 10, 2)->default(0);
            $table->decimal('other_allowances', 10, 2)->default(0);
            $table->decimal('total_salary', 10, 2);
            $table->date('effective_date');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->foreign('salary_level_id')->references('id')->on('salary_levels');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('salary_structures');
    }
}

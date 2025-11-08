<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeductionsBonusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // create_deductions_bonuses_table
        Schema::create('deductions_bonuses', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->enum('type', ['deduction', 'bonus']);
            $table->string('title');
            $table->decimal('amount', 10, 2);
            $table->enum('calculation_type', ['fixed', 'percentage'])->default('fixed');
            $table->text('description')->nullable();
            $table->date('effective_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->string('month_year'); // Format: YYYY-MM
            $table->boolean('applied')->default(false);
            $table->timestamps();
            
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
        Schema::dropIfExists('deductions_bonuses');
    }
}

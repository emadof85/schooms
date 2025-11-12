<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpenseRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       // create_expense_records_table
        Schema::create('expense_records', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('category_id');
            $table->string('title');
            $table->string('reference_no')->unique();
            $table->decimal('amount', 10, 2);
            $table->date('expense_date');
            $table->enum('payment_method', ['cash', 'bank_transfer', 'cheque', 'online'])->default('cash');
            $table->string('paid_to'); // Recipient name
            $table->text('description')->nullable();
            $table->string('attachment')->nullable();
            $table->unsignedInteger('recorded_by'); // Admin user
            $table->timestamps();
            
            $table->foreign('category_id')->references('id')->on('expense_categories');
            $table->foreign('recorded_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('expense_records');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentBusAssignmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_bus_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('student_record_id');
            $table->unsignedBigInteger('bus_assignment_id');
            $table->unsignedBigInteger('bus_stop_id');
            $table->decimal('fee', 8, 2);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->foreign('student_record_id')->references('id')->on('student_records')->onDelete('cascade');
            $table->foreign('bus_assignment_id')->references('id')->on('bus_assignments')->onDelete('cascade');
            $table->foreign('bus_stop_id')->references('id')->on('bus_stops')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('student_bus_assignments');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('student_record_id');
            $table->unsignedInteger('class_id')->nullable();
            $table->date('date')->index();
            $table->string('status')->default('present');
            $table->unsignedInteger('marked_by')->nullable();
            $table->timestamp('marked_at')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->unique(['student_record_id', 'date']);

            $table->foreign('student_record_id')->references('id')->on('student_records')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendances');
    }
};

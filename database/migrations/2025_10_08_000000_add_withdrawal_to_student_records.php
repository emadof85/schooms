<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWithdrawalToStudentRecords extends Migration
{
    public function up()
    {
        Schema::table('student_records', function (Blueprint $table) {
            $table->tinyInteger('wd')->default(0)->after('year_admitted');
            $table->date('wd_date')->nullable()->after('wd');
        });
    }

    public function down()
    {
        Schema::table('student_records', function (Blueprint $table) {
            $table->dropColumn(['wd', 'wd_date']);
        });
    }
}

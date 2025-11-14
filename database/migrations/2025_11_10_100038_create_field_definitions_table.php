<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFieldDefinitionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('field_definitions', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Field name (e.g., 'emergency_contact')
            $table->string('label'); // Display label (e.g., 'Emergency Contact')
            $table->string('type'); // Field type: text, textarea, select, date, number, checkbox, etc.
            $table->json('options')->nullable(); // For select/radio fields, store options as JSON
            $table->boolean('required')->default(false); // Is field required?
            $table->boolean('active')->default(true); // Is field active/visible?
            $table->integer('sort_order')->default(0); // For ordering fields
            $table->string('entity_type')->default('student'); // To extend to other entities later
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('field_definitions');
    }
}

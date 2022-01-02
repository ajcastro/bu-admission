<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('last_name')->default('');
            $table->string('first_name')->default('');
            $table->string('middle_name')->default('');
            $table->date('birthdate');
            $table->string('gender')->default('');
            $table->string('email')->default('');
            $table->string('mobile_number')->default('');
            $table->string('phone_number')->default('');
            $table->string('work_number')->default('');
            $table->string('company')->default('');
            $table->string('residence_address_line_1')->default('');
            $table->string('residence_address_line_2')->default('');
            $table->string('residence_municipality')->default('');
            $table->string('residence_province')->default('');
            $table->string('residence_zip_code')->default('');
            $table->string('residence_country')->default('');
            $table->boolean('same_with_residence_address')->default(0);
            $table->string('permanent_address_line_1')->default('');
            $table->string('permanent_address_line_2')->default('');
            $table->string('permanent_municipality')->default('');
            $table->string('permanent_province')->default('');
            $table->string('permanent_zip_code')->default('');
            $table->string('permanent_country')->default('');
            $table->string('status')->default('pending');
            $table->decimal('total_units', 8, 2)->default(0);
            $table->softDeletes();
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
        Schema::dropIfExists('applications');
    }
}

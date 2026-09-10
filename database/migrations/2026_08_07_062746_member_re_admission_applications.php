<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('member_re_admission_applications', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->string('username'); // Foreign
            $table->string('previous_academic_status');
            //  previous
            $table->string('previous_program')->nullable(); // Foreign
            $table->string('previous_program_type')->nullable(); // Foreign
            $table->string('previous_faculty')->nullable(); // Foreign
            $table->string('previous_department')->nullable(); // Foreign
            $table->year('previous_admission_session')->nullable();
            $table->year('previous_graduation_session')->nullable();
            $table->string('previous_matric_number')->nullable();

            // new
            $table->string('new_program')->nullable(); // Foreign
            $table->string('new_program_type')->nullable(); // Foreign
            $table->string('new_faculty')->nullable(); // Foreign
            $table->string('new_department')->nullable(); // Foreign
            $table->year('new_admission_session')->nullable();
            $table->string('new_matric_number')->nullable();

            $table->string('status')->default('pending');
            $table->string('processed_by')->nullable(); // Foreign
            $table->dateTime('processed_at')->nullable();
            $table->timestamps();


            $table->foreign('username')->references('username')->on('users')->onUpdate('cascade')->nullOnDelete();

            $table->foreign('previous_program')->references('slug')->on('programs')->onUpdate('cascade')->nullOnDelete();
            $table->foreign('previous_program_type')->references('slug')->on('program_types')->onUpdate('cascade')->nullOnDelete();
            $table->foreign('previous_faculty')->references('slug')->on('faculties')->onUpdate('cascade')->nullOnDelete();
            $table->foreign('previous_department')->references('slug')->on('departments')->onUpdate('cascade')->nullOnDelete();
            $table->foreign('processed_by')->references('username')->on('users')->onUpdate('cascade')->onDelete('cascade');

            $table->foreign('new_program')->references('slug')->on('programs')->onUpdate('cascade')->nullOnDelete();
            $table->foreign('new_program_type')->references('slug')->on('program_types')->onUpdate('cascade')->nullOnDelete();
            $table->foreign('new_faculty')->references('slug')->on('faculties')->onUpdate('cascade')->nullOnDelete();
            $table->foreign('new_department')->references('slug')->on('departments')->onUpdate('cascade')->nullOnDelete();

            $table->foreign('tenant_id')->references('id')->on('tenants')->nullOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_re_admission_applications');
    }
};

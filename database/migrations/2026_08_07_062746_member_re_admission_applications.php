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
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->string('username')->unique(); // Foreign
            $table->string('program')->nullable(); // Foreign
            $table->string('program_type')->nullable(); // Foreign
            $table->string('faculty')->nullable(); // Foreign
            $table->string('department')->nullable(); // Foreign
            $table->year('admission_session')->nullable();
            $table->year('graduation_session')->nullable();
            $table->string('matric_number')->nullable();
            $table->string('status')->default('pending');
            $table->string('processed_by')->nullable(); // Foreign
            $table->dateTime('processed_at')->nullable();
            $table->timestamps();


            $table->foreign('username')->references('username')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('program')->references('slug')->on('programs')->onUpdate('cascade')->nullOnDelete();
            $table->foreign('program_type')->references('slug')->on('program_types')->onUpdate('cascade')->nullOnDelete();
            $table->foreign('faculty')->references('slug')->on('faculties')->onUpdate('cascade')->nullOnDelete();
            $table->foreign('department')->references('slug')->on('departments')->onUpdate('cascade')->nullOnDelete();
            $table->foreign('processed_by')->references('username')->on('users')->onUpdate('cascade')->onDelete('cascade');
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

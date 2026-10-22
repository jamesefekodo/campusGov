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
        Schema::create('past_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->string('uuid')->unique();
            $table->string('slug')->unique();
            $table->string('program')->nullable(); // Foreign
            $table->string('program_type')->nullable(); // Foreign
            $table->string('faculty')->nullable(); // Foreign
            $table->string('department')->nullable(); // Foreign
            $table->string('semester');
            $table->string('course_code');
            $table->string('course_title');
            $table->integer('academic_year');
            $table->integer('level');
            $table->boolean('is_exam')->default(0);
            $table->longText('content')->nullable();
            $table->string('image_1')->nullable();
            $table->string('image_2')->nullable();
            $table->string('image_3')->nullable();
            $table->string('image_4')->nullable();
            $table->string('image_5')->nullable();
            $table->string('image_6')->nullable();

            $table->string('created_by')->nullable(); // Foreign

            $table->string('approved_by')->nullable(); // Foreign
            $table->string('approval_status')->default('pending');
            $table->dateTime('last_update')->nullable();
            $table->string('last_update_by')->nullable(); // Foreign
            $table->dateTime('last_approval_update')->nullable();

            $table->timestamps();




            $table->foreign('tenant_id')->references('id')->on('tenants')->nullOnDelete()->cascadeOnUpdate();
            $table->foreign('program')->references('slug')->on('programs')->onUpdate('cascade')->nullOnDelete();
            $table->foreign('program_type')->references('slug')->on('program_types')->onUpdate('cascade')->nullOnDelete();
            $table->foreign('faculty')->references('slug')->on('faculties')->onUpdate('cascade')->nullOnDelete();
            $table->foreign('department')->references('slug')->on('departments')->onUpdate('cascade')->nullOnDelete();

            $table->foreign('created_by')->references('username')->on('users')->onUpdate('cascade')->nullOnDelete();
            $table->foreign('approved_by')->references('username')->on('users')->onUpdate('cascade')->nullOnDelete();
            $table->foreign('last_update_by')->references('username')->on('users')->onUpdate('cascade')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('past_questions');
    }
};

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
        Schema::create('election_standard_votes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable(); // foreign
            $table->string('election_id'); // foreign
            $table->unsignedBigInteger('position'); // Foreign
            $table->string('candidate'); // foreign
            $table->string('voter'); // foreign

            $table->string('current_program')->nullable(); // Foreign
            $table->string('current_program_type')->nullable(); // Foreign
            $table->string('current_faculty')->nullable(); // Foreign
            $table->string('current_department')->nullable(); // Foreign
            $table->string('level_at_application');


            $table->timestamps();


            $table->foreign('tenant_id')->references('id')->on('tenants')->nullOnDelete()->cascadeOnUpdate();
            $table->foreign('candidate')->references('application_id')->on('election_candidate_applications')->onUpdate('cascade');
            $table->foreign('election_id')->references('election_id')->on('elections')->onDelete('cascade')->cascadeOnUpdate('cascade');
            $table->foreign('position')->references('id')->on('election_positions')->onUpdate('cascade');

            $table->foreign('voter')->references('username')->on('users')->onUpdate('cascade');

            $table->foreign('current_program')->references('slug')->on('programs')->onUpdate('cascade')->nullOnDelete();
            $table->foreign('current_program_type')->references('slug')->on('program_types')->onUpdate('cascade')->nullOnDelete();
            $table->foreign('current_faculty')->references('slug')->on('faculties')->onUpdate('cascade')->nullOnDelete();
            $table->foreign('current_department')->references('slug')->on('departments')->onUpdate('cascade')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('election_standard_votes');
    }
};

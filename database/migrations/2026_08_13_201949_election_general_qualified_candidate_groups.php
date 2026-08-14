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
        Schema::create(
            'election_general_candidate_groups',
            function (Blueprint $table) {
                $table->id();
                $table->string('uuid')->unique();
                $table->unsignedBigInteger('tenant_id')->nullable(); // foreign

                $table->string('election_id');

                $table->string('program')->nullable(); // Foreign
                $table->string('program_type')->nullable(); // Foreign
                $table->string('faculty')->nullable(); // Foreign
                $table->string('department')->nullable(); // Foreign

                $table->integer('start_level')->nullable();
                $table->integer('end_level')->nullable();


                $table->timestamps();



                $table->foreign('tenant_id')->references('id')->on('tenants')->nullOnDelete()->cascadeOnUpdate();
                $table->foreign('election_id')->references('election_id')->on('elections')->onDelete('cascade')->cascadeOnUpdate('cascade');
                $table->foreign('program')->references('slug')->on('programs')->onUpdate('cascade')->nullOnDelete();
                $table->foreign('program_type')->references('slug')->on('program_types')->onUpdate('cascade')->nullOnDelete();
                $table->foreign('faculty')->references('slug')->on('faculties')->onUpdate('cascade')->nullOnDelete();
                $table->foreign('department')->references('slug')->on('departments')->onUpdate('cascade')->nullOnDelete();
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('election_general_candidate_groups');
    }
};

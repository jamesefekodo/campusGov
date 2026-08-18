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
            'election_candidate_applications',
            function (Blueprint $table) {
                $table->id();
                $table->string('uuid')->unique();
                $table->unsignedBigInteger('tenant_id')->nullable(); // foreign

                $table->string('election_id'); // Foreign
                $table->unsignedBigInteger('position_id')->nullable(); // foreign

                $table->string('candidate'); // Foreign

                $table->string('application_type')->default('self');

                $table->string('current_program')->nullable(); // Foreign
                $table->string('current_program_type')->nullable(); // Foreign
                $table->string('current_application')->nullable(); // Foreign
                $table->string('current_department')->nullable(); // Foreign
                $table->string('level_at_application');

                $table->longText('bio');
                $table->longText('manifesto');
                $table->longText('stakeholders_remarks')->nullable();
                $table->string('application_form')->nullable(); // picture

                $table->string('initiated_by')->nullable(); // Foreign
                $table->string('approval_status')->default('pending');
                $table->dateTime('approved_at')->nullable();
                $table->string('processed_by')->nullable(); // Foreign


                $table->timestamps();






                $table->foreign('tenant_id')->references('id')->on('tenants')->nullOnDelete()->cascadeOnUpdate();
                $table->foreign('election_id')->references('election_id')->on('elections')->onDelete('cascade')->cascadeOnUpdate('cascade');
                $table->foreign('position_id')->references('id')->on('election_positions')->nullOnDelete()->cascadeOnUpdate();
                $table->foreign('current_program')->references('slug')->on('programs')->onUpdate('cascade')->nullOnDelete();
                $table->foreign('current_program_type')->references('slug')->on('program_types')->onUpdate('cascade')->nullOnDelete();
                $table->foreign('current_application')->references('slug')->on('faculties')->onUpdate('cascade')->nullOnDelete();
                $table->foreign('current_department')->references('slug')->on('departments')->onUpdate('cascade')->nullOnDelete();

                $table->foreign('candidate')->references('username')->on('users')->onUpdate('cascade');
                $table->foreign('initiated_by')->references('username')->on('users')->onUpdate('cascade');
                $table->foreign('processed_by')->references('username')->on('users')->onUpdate('cascade');
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('election_candidate_applications');
    }
};

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
        Schema::create('student_leadership_representative', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable(); // foreign
            $table->string('uuid')->unique();
            $table->string('username'); // foreign
            $table->string('position_slug'); // foreign
            $table->string('tenure_slug'); // foreign
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();


            $table->foreign('tenant_id')->references('id')->on('tenants')->nullOnDelete()->cascadeOnUpdate();
            $table->foreign('username')->references('username')->on('users')->onDelete('cascade')->cascadeOnUpdate();
            $table->foreign('position_slug')->references('slug')->on('student_leadership_positions')->onDelete('cascade')->cascadeOnUpdate();
            $table->foreign('tenure_slug')->references('slug')->on('student_leadership_tenures')->onDelete('cascade')->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_leadership_representative');
    }
};

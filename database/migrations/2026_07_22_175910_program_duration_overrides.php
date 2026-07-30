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
        Schema::create('program_duration_overrides', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->string('uuid')->unique();

            $table->string('program_slug');
            $table->string('program_type_slug')->nullable();
            $table->string('department_slug')->nullable();

            $table->unsignedTinyInteger('duration');

            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Constraints
            $table->foreign('program_slug')->references('slug')->on('programs')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('program_type_slug')->references('slug')->on('program_types')->onUpdate('cascade')->nullOnDelete();
            $table->foreign('department_slug')->references('slug')->on('departments')->onUpdate('cascade')->nullOnDelete();
            $table->foreign('tenant_id')->references('id')->on('tenants')->nullOnDelete()->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_duration_overrides');
    }
};

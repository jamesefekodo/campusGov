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
        Schema::create('member_skills', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->string('username'); // Foreign key
            $table->string('skill')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('username')
                ->references('username')->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreign('tenant_id')->references('id')->on('tenants')->nullOnDelete()->cascadeOnUpdate();

            // index
            $table->index('username');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_skills');
    }
};

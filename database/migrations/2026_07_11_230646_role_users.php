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
        Schema::create('role_users', function (Blueprint $table) { // pivot table for many-to-many relationship between users and roles
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('role_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->primary(['user_id', 'role_id']);


            $table->foreign('tenant_id')->references('id')->on('tenants')->nullOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_users');
    }
};

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
    { // pvot table for many-to-many relationship between roles and permissions
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->foreignId('role_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('permission_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->primary(['role_id', 'permission_id']);


            $table->foreignId('role_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('permission_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreign('tenant_id')->references('id')->on('tenants')->nullOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_permissions');
    }
};

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
        Schema::create('admin_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->string('username')->unique(); // Foreign
            $table->string('name')->nullable();
            $table->string('position')->nullable();
            $table->string('description')->nullable();

            $table->string('created_by')->nullable(); // Foreign
            $table->string('last_update_by')->nullable(); // Foreign
            $table->timestamps();


            $table->foreign('username')->references('username')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('created_by')->references('username')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('last_update_by')->references('username')->on('users')->onUpdate('cascade')->onDelete('cascade');

            $table->foreign('tenant_id')->references('id')->on('tenants')->nullOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_profiles');
    }
};

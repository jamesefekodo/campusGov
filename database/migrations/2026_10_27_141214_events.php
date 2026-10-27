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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable(); // foreign
            $table->string('uuid')->unique();
            $table->string('title');
            $table->text('description');
            $table->string('venue');
            $table->string('cover_image')->nullable();
            $table->dateTime('event_start');
            $table->dateTime('event_end');
            $table->boolean('is_published')->default(false);
            $table->string('created_by')->nullable(); // Foreign
            $table->string('approved_by')->nullable(); // Foreign
            $table->string('approval_status')->default('pending');
            $table->dateTime('last_update')->nullable();
            $table->string('last_update_by')->nullable(); // Foreign
            $table->dateTime('last_approval_update')->nullable();

            
            $table->timestamps();
            
            
            $table->foreign('tenant_id')->references('id')->on('tenants')->nullOnDelete()->cascadeOnUpdate();
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
        Schema::dropIfExists('events');
    }
};

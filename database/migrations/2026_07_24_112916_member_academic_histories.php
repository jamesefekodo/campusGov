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
        Schema::create('member_academic_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->string('username'); // Foreign key
            $table->string('institution')->nullable();
            $table->string('address')->nullable();
            $table->string('certificate')->nullable();
            $table->string('date', 7)->nullable(); // e.g. 2026-07
            $table->timestamps();


            // Foreign key constraint
            $table->foreign('username')
                ->references('username')->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            // index
            $table->index('username');
            $table->foreign('tenant_id')->references('id')->on('tenants')->nullOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_academic_histories');
    }
};

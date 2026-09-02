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
            'donations',
            function (Blueprint $table) {
                $table->id();
                $table->string('uuid')->unique();
                $table->unsignedBigInteger('tenant_id')->nullable(); // foreign

                $table->string('donation_id')->unique();
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('is_active')->default(false);

                $table->decimal('target_amount', 15, 2);
                $table->dateTime('deadline')->nullable();

                $table->string('created_by')->nullable(); // Foreign
                $table->string('processed_by')->nullable(); // Foreign
                $table->string('approval_status')->default('pending');
                $table->dateTime('approved_at')->nullable();
                $table->timestamps();



                $table->foreign('tenant_id')->references('id')->on('tenants')->nullOnDelete()->cascadeOnUpdate();
                $table->foreign('created_by')->references('username')->on('users')->onUpdate('cascade');
                $table->foreign('processed_by')->references('username')->on('users')->onUpdate('cascade');
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};

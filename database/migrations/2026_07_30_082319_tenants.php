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
        Schema::create('tenants', function (Blueprint $table) {

            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();

            $table->uuid('uuid')->unique();

            // Basic Information
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('code')->nullable()->unique();

            // Branding
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();

            // Contact
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();

            // Website
            $table->string('website')->nullable();

            // Multi-domain support
            $table->string('domain')->nullable()->unique();
            $table->string('subdomain')->nullable()->unique();

            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('is_verified')->default(false);

            // Subscription (future use)
            $table->date('subscription_starts_at')->nullable();
            $table->date('subscription_expires_at')->nullable();

            // Notes
            $table->longText('description')->nullable();

            $table->timestamps();



            $table->foreign('tenant_id')->references('id')->on('tenants')->nullOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};

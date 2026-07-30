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
        Schema::create('electronic_payment_methods', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->string('uuid')->unique();
            $table->string('slug')->unique();
            $table->string('name')->unique();
            $table->string('logo');
            $table->bigInteger('default_api');
            $table->boolean('is_active')->default(true);
            $table->timestamps();


            $table->foreign('default_api')->references('id')->on('electronic_payment_config')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('tenant_id')->references('id')->on('tenants')->nullOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('electronic_payment_methods');
    }
};

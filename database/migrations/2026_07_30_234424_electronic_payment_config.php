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
            'electronic_payment_config',
            function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tenant_id')->nullable();
                $table->string('payment_method'); //foreign
                $table->string('client_id')->nullable();
                $table->string('public_key')->nullable();
                $table->string('secret_key')->nullable();
                $table->string('encryption_key')->nullable();
                $table->string('version')->nullable();
                $table->boolean('is_active')->default(false);
                $table->timestamps();


                $table->foreign('payment_method')
                    ->references('slug')
                    ->on('electronic_payment_methods')
                    ->onUpdate('cascade');
                $table->foreign('tenant_id')->references('id')->on('tenants')->nullOnDelete()->cascadeOnUpdate();
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('electronic_payment_config');
    }
};

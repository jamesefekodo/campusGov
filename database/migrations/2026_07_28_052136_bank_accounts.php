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
            'bank_accounts',
            function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tenant_id')->nullable();
                $table->string('account_number');
                $table->string('account_name');
                $table->string('bank');
                $table->boolean('is_active')->default(true);
                $table->timestamps();



                $table->foreign('tenant_id')->references('id')->on('tenants')->nullOnDelete()->cascadeOnUpdate();
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_accounts');
    }
};

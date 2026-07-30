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
            'taxes',
            function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tenant_id')->nullable();
                $table->string('uuid'); // Foreign
                $table->string('tax_name')->unique();
                $table->string('tax_method');
                $table->string('tax_rate');
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
        Schema::dropIfExists('taxes');
    }
};

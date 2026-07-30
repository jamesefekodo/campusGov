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
        Schema::create('association_dues', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();

            $table->string('uuid'); // Foreign
            $table->string('fee_id')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('amount', 10, 2);
            $table->boolean('is_active')->default(true);
            $table->integer('academic_year')->nullable();
            // $table->string('academic_level');
            // $table->year('year');
            $table->timestamps();


            $table->foreign('tenant_id')->references('id')->on('tenants')->nullOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(table: 'association_dues');
    }
};

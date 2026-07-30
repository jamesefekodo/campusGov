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
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->unsignedBigInteger('tenant_id')->nullable();

            $table->string('slug')->unique();
            $table->string('name');
            $table->string('short_name');
            // $table->enum('type', [
            //     'fulltime',
            //     'parttime',
            //     'distance',
            //     'sandwich',
            //     'weekend'
            // ])->default('fulltime'); to another table for flexibility

            $table->string('level_format')->default('{value}');
            $table->enum('numbering', [
                'arabic',      // 1 2 3
                'roman',       // I  II  III
                'weighted'     // 100 200 300
            ])->default('arabic');

            $table->unsignedInteger('increment')->default(1);
            $table->unsignedTinyInteger('duration');
            $table->boolean('is_active')->default(true);
            $table->string('award'); // ND, HND, BSc, MSc, PGD, PhD
            $table->timestamps();


            $table->foreign('tenant_id')->references('id')->on('tenants')->nullOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};

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
            $table->string('uuid'); // Foreign
            $table->string('fee_id')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('amount', 10, 2);
            $table->boolean('is_active')->default(true);
            $table->integer('academic_year')->nullable();
            // $table->string('academic_level');
            $table->year('year');
            $table->timestamps();
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

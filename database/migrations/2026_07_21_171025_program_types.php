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
        Schema::create('program_types', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->string('slug')->unique();
            $table->string('program_slug'); // foreign
            $table->string('type_name');
            // $table->enum('type', [
            //     'fulltime',
            //     'parttime',
            //     'distance',
            //     'sandwich',
            //     'weekend'
            // ])->default('fulltime'); to another table for flexibility


            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_types');
    }
};

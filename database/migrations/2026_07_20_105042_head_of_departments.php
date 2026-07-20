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
        Schema::create('head_of_departments', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique(); // Foreign
            $table->string('department'); // Foreign
            $table->string('name');
            $table->string('qualifications')->nullable();
            $table->string('photo')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->longText('profile')->nullable();
            $table->boolean('is_active')->default(false);
            $table->date('assumed_office_on')->nullable();
            $table->date('tenure_end_on')->nullable();
            $table->timestamps();


            $table->foreign('department')->references('slug')->on('departments')->onUpdate('cascade')->onDelete('cascade');

            // Indexing
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('head_of_departments');
    }
};

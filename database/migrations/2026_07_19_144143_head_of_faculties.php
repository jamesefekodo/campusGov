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
        Schema::create('head_of_faculties', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->string('uuid')->unique();
            $table->string('faculty'); // Foreign
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


            $table->foreign('faculty')->references('slug')->on('faculties')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('tenant_id')->references('id')->on('tenants')->nullOnDelete()->cascadeOnUpdate();

            // Indexing
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('head_of_faculties');
    }
};

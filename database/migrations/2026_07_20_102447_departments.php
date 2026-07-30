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
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->string('uuid')->unique();
            $table->string('slug')->unique();
            $table->string('faculty'); //Foreign
            $table->string('name');
            $table->string('administrative_head_title');
            $table->longText('description');
            $table->boolean('is_active')->default(true);
            $table->timestamps();


            $table->foreign('faculty')->references('slug')->on('faculties')->onUpdate('cascade');
            $table->foreign('tenant_id')->references('id')->on('tenants')->nullOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};

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
        Schema::create('recommended_courses', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('created_by'); // Foreign
            $table->string('title');
            $table->longText('description');
            $table->string('cover_image')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();


            $table->foreign('created_by')
                ->references('username')
                ->on('users')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recommended_courses');
    }
};

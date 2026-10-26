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
        Schema::create('frontend_themes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('description')->nullable();
             $table->string('view_path');
            $table->boolean('is_active')->default(true);
            $table->json('default_colors');
            $table->timestamps();
        });

        Schema::create('tenant_theme_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('frontend_theme_id')->constrained()->cascadeOnDelete();
            $table->json('color_overrides')->nullable();
            $table->timestamps();

            $table->unique('tenant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_theme_settings');
        Schema::dropIfExists('frontend_themes');
    }
};

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
        Schema::create('basic_system_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('current_academic_year');
            $table->boolean('maintenance_mode')->default(false);
            $table->text('association_name')->nullable();
            $table->string('association_acronym')->nullable();
            $table->string('association_logo')->nullable();
            $table->text('association_slogan')->nullable();
            $table->text('school_name')->nullable();
            $table->string('school_acronym')->nullable();
            $table->string('school_logo')->nullable();
            $table->text('phone')->nullable();
            $table->text('email')->nullable();
            $table->text('address')->nullable();
            $table->text('facebook_link')->nullable();
            $table->text('instagram_link')->nullable();
            $table->text('twitter_link')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('basic_system_settings');
    }
};

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
        Schema::create('member_social_media_handles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->string('username'); // Foreign
            $table->string('platform'); // Foreign
            $table->string('handle');
            $table->string('link');
            $table->timestamps();



            $table->foreign('username')->references('username')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('platform')->references('slug')->on('recognized_social_media_platforms')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('tenant_id')->references('id')->on('tenants')->nullOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_social_media_handles');
    }
};

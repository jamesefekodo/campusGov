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
        Schema::create('media_publications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->uuid('uuid')->unique();

            $table->string('author')->nullable(); // Foreign
            $table->string('title');
            $table->longText('content');
            $table->string('cover_image')->nullable();
            $table->string('meta_description');
            $table->text('meta_keywords');
            $table->unsignedInteger('views')->default(0);
            $table->unsignedInteger('shares')->default(0);
            $table->string('approved_by')->nullable(); // Foreign
            $table->string('approval_status')->default('pending');
            $table->dateTime('published_at')->nullable();
            $table->boolean('is_active')->default(1);

            $table->timestamps();




            $table->foreign('tenant_id')->references('id')->on('tenants')->nullOnDelete()->cascadeOnUpdate();
            $table->foreign('author')->references('username')->on('users')->onUpdate('cascade')->nullOnDelete();
            $table->foreign('approved_by')->references('username')->on('users')->onUpdate('cascade')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_publications');
    }
};

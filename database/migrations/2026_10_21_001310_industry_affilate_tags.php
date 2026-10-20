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
        Schema::create('industry_affiliate_tags', function (Blueprint $table) {
            $table->id();

            $table->foreignId('industry_affiliate_id')
                ->constrained('industry_affiliates')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('tag_for_industry_id')
                ->constrained('tags_for_industry_affiliates')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(
                ['industry_affiliate_id', 'tag_for_industry_id'],
                'course_tag_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('industry_affiliate_tags');
    }
};

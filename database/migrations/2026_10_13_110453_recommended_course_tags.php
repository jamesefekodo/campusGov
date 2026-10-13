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
       Schema::create('recommended_course_tags', function (Blueprint $table) {
            $table->id();

            $table->foreignId('recommended_course_id')
                ->constrained('recommended_courses')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('tag_for_course_id')
                ->constrained('tags_for_recommended_books')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(
                ['recommended_course_id', 'tag_for_course_id'],
                'course_tag_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recommended_course_tags');
    }
};

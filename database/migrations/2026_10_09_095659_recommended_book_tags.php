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
        Schema::create('recommended_book_tags', function (Blueprint $table) {
            $table->id();

            $table->foreignId('recommended_book_id')
                ->constrained('recommended_books')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('tag_for_book_id')
                ->constrained('tags_for_recommended_books')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(
                ['recommended_book_id', 'tag_for_book_id'],
                'book_tag_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recommended_book_tags');
    }
};

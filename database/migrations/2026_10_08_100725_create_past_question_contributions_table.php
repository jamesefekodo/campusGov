<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('past_question_contributions', function (Blueprint $table) {
            $table->id();

            $table->uuid('uuid')->unique();

            $table->foreignId('past_question_id')
                ->constrained('past_questions')
                ->cascadeOnDelete();

            $table->string('username');

            $table->longText('content');

            $table->timestamp('edited_at')->nullable();

            $table->timestamps();

            $table->foreign('username')
                ->references('username')
                ->on('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->index([
                'past_question_id',
                'created_at',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('past_question_contributions');
    }
};
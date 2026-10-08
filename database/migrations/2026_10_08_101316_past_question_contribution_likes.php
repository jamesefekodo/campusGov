<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('past_question_contribution_likes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('contribution_id')
                ->constrained('past_question_contributions')
                ->cascadeOnDelete();

            $table->string('username');

            $table->timestamps();

            $table->foreign('username')
                ->references('username')
                ->on('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->unique([
                'contribution_id',
                'username',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('past_question_contribution_likes');
    }
};
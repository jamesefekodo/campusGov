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
        Schema::create('member_awards', function (Blueprint $table) {
            $table->id();
            $table->string('username'); // Foreign key, NOT unique
            $table->string('award')->nullable();
            $table->string('organization')->nullable();
            $table->date('date')->nullable();

            $table->timestamps();

            // Foreign key constraint
            $table->foreign('username')
                ->references('username')->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            // index
            $table->index('username');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_awards');
    }
};

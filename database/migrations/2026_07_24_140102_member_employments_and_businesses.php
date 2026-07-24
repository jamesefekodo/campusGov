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
        Schema::create('member_employments_and_businesses', function (Blueprint $table) {
            $table->id();
            $table->string('username'); // Foreign key, NOT unique
            $table->string('business')->nullable();
            $table->string('address')->nullable();
            $table->string('position')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
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
        Schema::dropIfExists('member_employments_and_businesses');
    }
};

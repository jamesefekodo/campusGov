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
        Schema::create('role_users', function (Blueprint $table) {
            $table->id();
            $table->string('username'); // Foreign
            $table->unsignedBigInteger('role_id'); // Foreign
            $table->timestamps();


            $table->foreign('username')->references('username')->on('users')->onUpdate('cascade');
            $table->foreign('role_id')->references('id')->on('roles')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_users');
    }
};

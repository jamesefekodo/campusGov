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
        Schema::create('member_personal_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique(); // Foreign
            $table->string('phone1'); // Foreign
            $table->string('email1'); // Foreign
            $table->string('phone2')->nullable();
            $table->string('email2')->nullable();
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('gender')->nullable();
            $table->date('dob')->nullable();
            $table->string('nationality')->nullable();
            $table->string('state_of_origin')->nullable();
            $table->string('lga_of_origin')->nullable();
            $table->string('address1')->nullable();
            $table->string('address2')->nullable();
            $table->boolean('is_verified')->default(true);
            $table->string('verified_by')->nullable(); // Foreign
            $table->dateTime('verified_at')->nullable();
            $table->timestamps();


            $table->foreign('username')->references('username')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('verified_by')->references('username')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('phone1')->references('phone')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('email1')->references('email')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_personal_profiles');
    }
};

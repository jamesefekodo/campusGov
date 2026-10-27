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
        Schema::create('basic_system_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->integer('current_academic_year');
            $table->boolean('maintenance_mode')->default(false);
            $table->text('association_name')->nullable();
            $table->string('association_acronym')->nullable();
            $table->string('association_logo')->nullable();
            $table->text('association_slogan')->nullable();
            $table->longText('description')->nullable();
            $table->string('operational_level')->default('department'); // department, faculty (+school), institute
            $table->text('school_name')->nullable();
            $table->boolean('is_polytechnic')->default(true);
            $table->string('school_acronym')->nullable();
            $table->string('school_logo')->nullable();
            $table->text('phone')->nullable();
            $table->text('email')->nullable();
            $table->text('address')->nullable();
            $table->text('facebook_link')->nullable();
            $table->text('instagram_link')->nullable();
            $table->text('twitter_link')->nullable();
            $table->text('primary_colour')->nullable();
            $table->text('secondary_colour')->nullable();
            $table->text('tertiary_colour')->nullable();
            $table->text('footer_plus')->nullable();
            $table->integer('school_founding_year');
            $table->decimal('account_balance', 15, 2)->default(0);
            $table->longText('president_remarks')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->nullOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('basic_system_settings');
    }
};

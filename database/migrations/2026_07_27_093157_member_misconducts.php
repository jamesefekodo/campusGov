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
        Schema::create('member_misconducts', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->string('username'); // Foreign
            $table->string('case_id')->unique();
            $table->date('incident_date');
            $table->date('report_date');
            $table->string('reported_by');
            $table->string('reporter_username')->nullable(); // foreign
            $table->string('misconduct_type');
            $table->longText('description');
            $table->longText('witness_report')->nullable();
            $table->longText('disciplinary_action')->nullable();
            $table->string('attachment_1')->nullable();
            $table->string('attachment_2')->nullable();
            $table->string('status');
            $table->timestamps();



            $table->foreign('username')->references('username')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('reporter_username')->references('username')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('tenant_id')->references('id')->on('tenants')->nullOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_misconducts');
    }
};

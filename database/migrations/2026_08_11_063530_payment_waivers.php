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
        Schema::create(
            'payment_waivers',
            function (Blueprint $table) {
                $table->id();
                $table->string('uuid')->unique();
                $table->unsignedBigInteger('tenant_id')->nullable(); // foreign

                $table->string('waiver_id')->unique();
                $table->string('username'); // Foreign
                $table->string('due_id')->nullable(); // Foreign

                $table->text('reason')->nullable();

                $table->string('initiated_by')->nullable(); // Foreign
                $table->dateTime('initiated_at')->nullable();
                $table->string('processed_by')->nullable(); // Foreign
                $table->string('approval_status')->default('pending');
                $table->dateTime('approved_at')->nullable();
                $table->timestamps();



                $table->foreign('tenant_id')->references('id')->on('tenants')->nullOnDelete()->cascadeOnUpdate();
                $table->foreign('username')->references('username')->on('users')->onUpdate('cascade');
                $table->foreign('due_id')->references('fee_id')->on('association_dues')->onUpdate('cascade');
                $table->foreign('initiated_by')->references('username')->on('users')->onUpdate('cascade');
                $table->foreign('processed_by')->references('username')->on('users')->onUpdate('cascade');
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_waivers');
    }
};

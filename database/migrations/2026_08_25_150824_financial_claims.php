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
            'financial_claims',
            function (Blueprint $table) {
                $table->id();
                $table->string('uuid')->unique();
                $table->unsignedBigInteger('tenant_id')->nullable(); // foreign

                $table->string('claim_id')->unique();
                $table->decimal('amount', 15, 2);
                $table->date('date');

                $table->text('description')->nullable();

                $table->boolean('balance_update')->default(false);
                $table->text('remarks')->nullable();

                $table->string('claim_type'); // credit / debit


                $table->string('initiated_by')->nullable(); // Foreign
                $table->dateTime('initiated_at')->nullable();
                $table->string('processed_by')->nullable(); // Foreign
                $table->string('approval_status')->default('pending');
                $table->dateTime('approved_at')->nullable();
                $table->timestamps();




                $table->foreign('tenant_id')->references('id')->on('tenants')->nullOnDelete()->cascadeOnUpdate();
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
        Schema::dropIfExists('financial_claims');
    }
};

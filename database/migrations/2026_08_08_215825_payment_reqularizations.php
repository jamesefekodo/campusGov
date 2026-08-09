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
            'payment_regularizations',
            function (Blueprint $table) {
                $table->id();
                $table->string('uuid')->unique();
                $table->unsignedBigInteger('tenant_id')->nullable(); // foreign

                $table->string('regularization_id')->unique();
                $table->string('username'); // Foreign
                $table->string('due_id')->nullable(); // Foreign
                $table->decimal('amount_paid', 15, 2);
                $table->date('payment_date');
                $table->unsignedBigInteger('bank_account_id')->nullable();

                $table->string('electronic_payment_method')->nullable(); // foreign
                $table->string('manual_payment_method')->nullable();
                $table->string('proof_of_payment')->nullable();

                $table->text('description')->nullable();
                $table->text('remarks')->nullable();

                $table->string('payment_mode')->default('manual');


                $table->string('initiated_by')->nullable(); // Foreign
                $table->dateTime('initiated_at')->nullable();
                $table->string('processed_by')->nullable(); // Foreign
                $table->string('approval_status')->default('pending');
                $table->dateTime('approved_at')->nullable();
                $table->timestamps();




                $table->foreign('tenant_id')->references('id')->on('tenants')->nullOnDelete()->cascadeOnUpdate();
                $table->foreign('username')->references('username')->on('users')->onUpdate('cascade');
                $table->foreign('due_id')->references('fee_id')->on('association_dues')->onUpdate('cascade');
                $table->foreign('electronic_payment_method')->references('slug')->on('electronic_payment_methods')->onUpdate('cascade');
                $table->foreign('initiated_by')->references('username')->on('users')->onUpdate('cascade');
                $table->foreign('processed_by')->references('username')->on('users')->onUpdate('cascade');
                $table->foreign('bank_account_id')->references('id')->on('bank_accounts')->restrictOnDelete();
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_regularizations');
    }
};

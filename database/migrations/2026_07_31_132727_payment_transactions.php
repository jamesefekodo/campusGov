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
            'payment_transactions',
            function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tenant_id')->nullable();

                $table->string('payment_transaction_id')->unique();
                $table->string('service_provider_id')->nullable();
                $table->string('paystack_reference')->nullable();
                $table->string('payment_mode')->default('electronic');
                $table->string('registered_payer')->nullable(); // Foreign
                $table->string('payment_type')->nullable();

                $table->string('non_registered_payer')->nullable();
                $table->string('phone')->nullable();
                $table->string('email')->nullable();

                $table->string('payment_purpose');
                $table->string('due_id')->nullable(); // Foreign
                $table->string('invoice_id')->nullable(); // Foreign
                $table->string('donation_id')->nullable(); // Foreign
                $table->string('election_id')->nullable(); // Foreign
                $table->unsignedBigInteger('election_position')->nullable(); // Foreign

                $table->unsignedBigInteger('election_candidate_application')->nullable(); // Foreign   
                $table->string('election_candidate_voted')->nullable(); // Foreign                


                $table->decimal('amount_payable', 15, 2)->nullable();
                $table->decimal('amount_paid', 15, 2);
                $table->decimal('amount_settled', 15, 2)->nullable();
                $table->decimal('service_charge', 15, 2)->nullable();
                $table->decimal('total_tax', 15, 2)->nullable();

                $table->text('taxes_paid')->nullable();
                $table->string('electronic_payment_method')->nullable(); // foreign
                $table->string('manual_payment_method')->nullable();
                $table->string('photo')->nullable();
                $table->string('payment_status')->default('waiting');
                $table->dateTime('paid_at');
                $table->string('approval_status');
                $table->string('balance_update_status')->nullable()->default('pending');
                $table->string('approved_by')->nullable(); // foreign
                $table->dateTime('approved_at')->nullable();
                $table->timestamps();




                $table->foreign('registered_payer')
                    ->references('username')
                    ->on('users')
                    ->onUpdate('cascade');

                $table->foreign('approved_by')
                    ->references('username')
                    ->on('users')
                    ->onUpdate('cascade');


                $table->foreign('electronic_payment_method')
                    ->references('slug')
                    ->on('electronic_payment_methods')
                    ->onUpdate('cascade');

                $table->foreign('due_id')
                    ->references('fee_id')
                    ->on('association_dues')
                    ->onUpdate('cascade');
                $table->foreign('tenant_id')->references('id')->on('tenants')->nullOnDelete()->cascadeOnUpdate();



                // $table->foreign('invoice_id')
                //     ->references('invoice_number')
                //     ->on('association_invoices')
                //     ->onUpdate('cascade');

                $table->foreign('election_id')
                    ->references('election_id')
                    ->on('elections')
                    ->onUpdate('cascade');

                $table->foreign('election_position')
                    ->references('id')
                    ->on('election_positions')
                    ->onUpdate('cascade');

                $table->foreign('election_candidate_application')
                    ->references('id')
                    ->on('election_candidate_applications')
                    ->onUpdate('cascade');

                $table->foreign('election_candidate_voted')
                    ->references('application_id')
                    ->on('election_candidate_applications')
                    ->onUpdate('cascade');
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};

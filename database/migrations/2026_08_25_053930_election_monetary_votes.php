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
        Schema::create('election_monetary_votes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable(); // foreign
            $table->string('election_id'); // foreign
            $table->unsignedBigInteger('position'); // Foreign
            $table->string('candidate'); // foreign
            $table->string('registered_voter')->nullable(); // foreign

            $table->string('unregistered_voter')->nullable();
            $table->string('voter_email')->nullable();
            $table->string('voter_phone')->nullable();

            $table->integer('no_of_votes')->nullable();
            $table->decimal('amount_paid', 15, 2);
            $table->decimal('cost_per_vote', 15, 2);
            $table->boolean('votes_counted')->default(false);


            $table->string('payment_transaction_id'); // foreign





            $table->timestamps();


            $table->foreign('tenant_id')->references('id')->on('tenants')->nullOnDelete()->cascadeOnUpdate();
            $table->foreign('candidate')->references('application_id')->on('election_candidate_applications')->onUpdate('cascade');
            $table->foreign('election_id')->references('election_id')->on('elections')->onDelete('cascade')->cascadeOnUpdate('cascade');
            $table->foreign('position')->references('id')->on('election_positions')->onUpdate('cascade');

            $table->foreign('registered_voter')->references('username')->on('users')->onUpdate('cascade');

            $table->foreign('payment_transaction_id')->references('payment_transaction_id')->on('payment_transactions')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('election_monetary_votes');
    }
};

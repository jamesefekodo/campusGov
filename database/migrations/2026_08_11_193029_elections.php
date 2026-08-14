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
            'elections',
            function (Blueprint $table) {
                $table->id();
                $table->string('uuid')->unique();
                $table->unsignedBigInteger('tenant_id')->nullable(); // foreign

                $table->string('election_id')->unique();
                $table->string('voting_method')->nullable('standard');
                $table->boolean('public_visibility')->default(false);
                $table->string('title');
                $table->integer('academic_session');
                $table->text('description')->nullable();

                $table->string('status')->default('draft'); // draft, awaiting_approval new, registration, screening, campaign, voting, result_pending, completed, cancelled, suspended

                $table->dateTime('form_sales_starts');
                $table->dateTime('form_sales_ends');

                $table->dateTime('screening_starts')->nullable();
                $table->dateTime('screening_ends')->nullable();

                $table->dateTime('campaign_starts')->nullable();
                $table->dateTime('campaign_ends')->nullable();

                $table->dateTime('voting_starts');
                $table->dateTime('voting_ends');
                $table->decimal('cost_per_vote', 10, 2)->nullable();

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
        Schema::dropIfExists('elections');
    }
};

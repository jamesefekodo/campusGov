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
            'invoices',
            function (Blueprint $table) {
                $table->id();
                $table->string('uuid')->unique();
                $table->unsignedBigInteger('tenant_id')->nullable(); // foreign

               $table->string('invoice_number')->unique();
                $table->string('registered_payer')->nullable(); // Foreign
                $table->string('non_registered_payer')->nullable();
                $table->decimal('amount', 10, 2);
                $table->text('description')->nullable();
                $table->date('issue_date')->nullable();
                $table->dateTime('due_date')->nullable();
                $table->string('status')->default('pending');

                $table->string('created_by')->nullable(); // Foreign
                $table->timestamps();



                $table->foreign('tenant_id')->references('id')->on('tenants')->nullOnDelete()->cascadeOnUpdate();
                $table->foreign('created_by')->references('username')->on('users')->onUpdate('cascade');
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};

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
            'election_positions',
            function (Blueprint $table) {
                $table->id();
                $table->string('uuid')->unique();
                $table->unsignedBigInteger('tenant_id')->nullable(); // foreign

                $table->string('election_id');

                $table->string('position')->nullable();
                $table->text('description');
                $table->decimal('application_fee', 10, 2);

                $table->timestamps();


                $table->foreign('tenant_id')->references('id')->on('tenants')->nullOnDelete()->cascadeOnUpdate();
                $table->foreign('election_id')->references('election_id')->on('elections')->onDelete('cascade')->cascadeOnUpdate('cascade');
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('election_positions');
    }
};

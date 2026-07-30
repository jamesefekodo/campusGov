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
        Schema::create('member_leaderhip_positions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->string('username'); // Foreign key
            $table->string('organization')->nullable();
            $table->integer('is_current')->default(0);
            $table->string('address')->nullable();
            $table->string('position')->nullable();
            $table->string('start_date', 7)->nullable(); // e.g. 2026-07
            $table->string('end_date', 7)->nullable(); // e.g. 2026-07
            $table->timestamps();


            // Foreign key constraint
            $table->foreign('username')
                ->references('username')->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreign('tenant_id')->references('id')->on('tenants')->nullOnDelete()->cascadeOnUpdate();

            // index
            $table->index('username');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_leaderhip_positions');
    }
};

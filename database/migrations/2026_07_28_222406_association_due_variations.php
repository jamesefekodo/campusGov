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
            'association_due_variations',
            function (Blueprint $table) {
                $table->id();
                $table->string('due_id'); //foreign
                $table->year('admission_session')->nullable();
                $table->string('program_slug')->nullable();
                $table->string('program_type_slug')->nullable();
                $table->timestamps();


                $table->foreign('due_id')
                    ->references('fee_id')
                    ->on('association_dues')
                    ->onUpdate('cascade');
                $table->foreign('program_slug')->references('slug')->on('programs')->onUpdate('cascade')->onDelete('cascade');
                $table->foreign('program_type_slug')->references('slug')->on('program_types')->onUpdate('cascade')->nullOnDelete();
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('association_due_variations');
    }
};

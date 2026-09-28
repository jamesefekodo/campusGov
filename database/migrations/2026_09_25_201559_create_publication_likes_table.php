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
        Schema::create('publication_likes', function (Blueprint $table) {
            $table->id();

            /*
             * Multi-tenant ownership.
             */
            $table->unsignedBigInteger('tenant_id')->nullable();

            /*
             * The publication/post being liked.
             */
            $table->unsignedBigInteger('media_publication_id');

            /*
             * User who liked this publication.
             */
            $table->string('username')->nullable();

            $table->timestamps();

            /*
             * One username can like one publication only once.
             */
            $table->unique(
                ['media_publication_id', 'username'],
                'unique_publication_username_like'
            );

            $table->index([
                'tenant_id',
                'media_publication_id',
            ], 'publication_like_lookup_index');

            $table->index('username');

            $table->foreign('tenant_id')
                ->references('id')
                ->on('tenants')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->foreign('media_publication_id')
                ->references('id')
                ->on('media_publications')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            /*
             * If a user is deleted, retain the like record
             * but set its username to null.
             */
            $table->foreign('username')
                ->references('username')
                ->on('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publication_likes');
    }
};

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
        Schema::create('publication_comment_likes', function (Blueprint $table) {
            $table->id();

            /*
             * Multi-tenant ownership.
             */
            $table->unsignedBigInteger('tenant_id')->nullable();

            /*
             * Can be a normal comment or a reply because both are
             * stored in publication_comments.
             */
            $table->unsignedBigInteger('publication_comment_id');

            /*
             * User who liked the comment or reply.
             */
            $table->string('username')->nullable();

            $table->timestamps();

            /*
             * One user can like one comment/reply only once.
             */
            $table->unique(
                ['publication_comment_id', 'username'],
                'unique_comment_username_like'
            );

            $table->index([
                'tenant_id',
                'publication_comment_id',
            ], 'publication_comment_like_lookup_index');

            $table->index('username');

            $table->foreign('tenant_id')
                ->references('id')
                ->on('tenants')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            /*
             * If a comment or reply is deleted:
             * all likes on it are deleted automatically.
             */
            $table->foreign('publication_comment_id')
                ->references('id')
                ->on('publication_comments')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreign('username')
                ->references('username')
                ->on('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publication_comment_likes');
    }
};

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
        Schema::create('publication_comments', function (Blueprint $table) {
            $table->id();

            /*
             * Safe public identifier for URLs, AJAX, update and delete actions.
             */
            $table->uuid('uuid')->unique();

            /*
             * Multi-tenant ownership.
             */
            $table->unsignedBigInteger('tenant_id')->nullable();

            /*
             * Publication/post receiving the comment.
             */
            $table->unsignedBigInteger('media_publication_id');

            /*
             * Null means main comment.
             * A value means this record is a reply to another comment.
             */
            $table->unsignedBigInteger('parent_id')->nullable();

            /*
             * Username of the user who wrote the comment or reply.
             *
             * Ensure this length matches users.username exactly.
             * If your users.username is string(100), use string('username', 100).
             */
            $table->string('username');

            /*
             * Main comment or reply text.
             */
            $table->text('comment');

            /*
             * pending, approved, rejected.
             */
            $table->string('status')->default('approved');

            /*
             * Lets an admin hide the comment without deleting it.
             */
            $table->boolean('is_active')->default(true);

            /*
             * Set this when a user edits their comment.
             */
            $table->timestamp('edited_at')->nullable();

            $table->timestamps();

            /*
             * Faster queries when loading comments for a publication.
             */
            $table->index([
                'tenant_id',
                'media_publication_id',
                'parent_id',
                'status',
                'is_active',
            ], 'publication_comments_display_index');

            $table->index('username')->nullable();

            /*
             * Foreign keys.
             */
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
             * When a main comment is permanently deleted,
             * its replies will also be deleted.
             */
            $table->foreign('parent_id')
                ->references('id')
                ->on('publication_comments')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            /*
             * The users.username column MUST be unique.
             *
             * If a user is deleted, retain the comment but make
             * username null instead of deleting every comment.
             */
            // $table->foreign('username')
            //     ->references('username')
            //     ->on('users')
            //     ->nullOnDelete()
            //     ->cascadeOnUpdate();

            $table->foreign('username')
                ->references('username')
                ->on('users')
                ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publication_comments');
    }
};
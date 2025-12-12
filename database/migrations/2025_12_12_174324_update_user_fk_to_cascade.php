<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // =========================
        // friend
        // =========================
        Schema::table('friend', function (Blueprint $table) {
            $table->dropForeign(['user_id_1']);
            $table->dropForeign(['user_id_2']);

            $table->foreign('user_id_1')
                ->references('user_id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('user_id_2')
                ->references('user_id')
                ->on('users')
                ->onDelete('cascade');
        });

        // =========================
        // discussion_like
        // =========================
        Schema::table('discussion_like', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['discus_id']);

            $table->foreign('user_id')
                ->references('user_id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('discus_id')
                ->references('discus_id')
                ->on('discussion')
                ->onDelete('cascade');
        });

        // =========================
        // community_user
        // =========================
        Schema::table('community_user', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')
                ->references('user_id')
                ->on('users')
                ->onDelete('cascade');
        });

        // =========================
        // chat_room
        // =========================
        Schema::table('chat_room', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->foreign('created_by')
                ->references('user_id')
                ->on('users')
                ->onDelete('cascade');
        });

        // =========================
        // chat_member
        // =========================
        Schema::table('chat_member', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')
                ->references('user_id')
                ->on('users')
                ->onDelete('cascade');
        });

        // =========================
        // chat_message
        // =========================
        Schema::table('chat_message', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')
                ->references('user_id')
                ->on('users')
                ->onDelete('cascade');
        });

        // =========================
        // user_experiences
        // =========================
        Schema::table('user_experiences', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')
                ->references('user_id')
                ->on('users')
                ->onDelete('cascade');
        });

        // =========================
        // posts
        // =========================
        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['wall_owner_id']);
            $table->dropForeign(['author_id']);

            $table->foreign('wall_owner_id')
                ->references('user_id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('author_id')
                ->references('user_id')
                ->on('users')
                ->onDelete('cascade');
        });

        // =========================
        // post_comments
        // =========================
        Schema::table('post_comments', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')
                ->references('user_id')
                ->on('users')
                ->onDelete('cascade');
        });

        // =========================
        // post_likes
        // =========================
        Schema::table('post_likes', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')
                ->references('user_id')
                ->on('users')
                ->onDelete('cascade');
        });

        // =========================
        // favorite_friends
        // =========================
        Schema::table('favorite_friends', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['friend_user_id']);

            $table->foreign('user_id')
                ->references('user_id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('friend_user_id')
                ->references('user_id')
                ->on('users')
                ->onDelete('cascade');
        });

        // =========================
        // users_blocked
        // =========================
        Schema::table('users_blocked', function (Blueprint $table) {
            $table->dropForeign(['blocker_user_id']);
            $table->dropForeign(['blocked_user_id']);

            $table->foreign('blocker_user_id')
                ->references('user_id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('blocked_user_id')
                ->references('user_id')
                ->on('users')
                ->onDelete('cascade');
        });

        // =========================
        // discussion_comment
        // =========================
        Schema::table('discussion_comment', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')
                ->references('user_id')
                ->on('users')
                ->onDelete('cascade');
        });

        // =========================
        // discussion_comment_like
        // =========================
        Schema::table('discussion_comment_like', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')
                ->references('user_id')
                ->on('users')
                ->onDelete('cascade');
        });

        // =========================
        // discussion
        // =========================
        Schema::table('discussion', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')
                ->references('user_id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        // Optional: rollback bisa dikembalikan FK tanpa cascade
    }
};

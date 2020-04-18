<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LinkUserAvatarToImages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'users',
            function (Blueprint $table) {
                $table->index('avatarImageId');
                $table->foreign(['avatarImageId'], 'user_avatar_image')
                    ->references(['id'])
                    ->on('images')
                    ->onDelete('SET NULL')
                    ->onUpdate('CASCADE');
            }
        );

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(
            'users',
            function (Blueprint $table) {
                $table->dropForeign('user_avatar_image');
                $table->dropIndex('users_avatarimageid_index');
            }
        );
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration auto-generated by Sequel Pro Laravel Export (1.5.0)
 *
 * @see https://github.com/cviebrock/sequel-pro-laravel-export
 */
class CreatePasswordResetTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'password_reset_tokens',
            function (Blueprint $table) {
                $table->string('token', 64);
                $table->unsignedInteger('userId');
                \Antriver\LaravelSiteUtils\Migrations\MigrationHelper::addCreatedAt($table);

                $table->primary('token');

                $table->foreign('userId', 'password_reset_tokens_user')
                    ->references('id')
                    ->on('users')
                    ->onDelete('CASCADE')
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
        Schema::dropIfExists('password_reset_tokens');
    }
}

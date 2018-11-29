<?php

/**
 * PHP Version 7.2
 *
 * @category TestMigrations
 * @package  LaravelExtensions
 * @author   Faísca Criativa <developers@faiscacriativa.com.br>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/faiscacriativa/laravel-extensions/blob/master/src/tests/database/migrations/2014_10_12_000000_create_users_table.php
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migração para criar a tabela "users".
 *
 * @category TestMigrations
 * @package  LaravelExtensions
 * @author   Faísca Criativa <developers@faiscacriativa.com.br>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/faiscacriativa/laravel-extensions/blob/master/src/tests/database/migrations/2014_10_12_000000_create_users_table.php
 */
class CreateUsersTable extends Migration
{
    /**
     * Run the migration.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'users',
            function (Blueprint $table) {
                $table->increments('id');
                $table->string('email')->unique();
                $table->string('password');
                $table->rememberToken();
                $table->timestamps();
                $table->timestamp('email_verified_at')->nullable();
            }
        );
    }

    /**
     * Revert the migration.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}

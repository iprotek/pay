<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppUserAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_user_accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('oauth_client_id');
            $table->bigInteger('app_user_id');
            $table->text('password');
            $table->boolean('is_blocked')->nullable();
            $table->bigInteger('blocked_by')->nullable();
            $table->string('provider')->default('app_users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('app_user_accounts');
    }
}

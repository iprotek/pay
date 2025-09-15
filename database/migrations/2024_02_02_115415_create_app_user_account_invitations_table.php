<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppUserAccountInvitationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_user_account_invitations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('app_user_id');
            $table->bigInteger('oauth_client_id');
            $table->string('app_name');
            $table->string('email');
            $table->bigInteger('group_id');
            $table->bigInteger('app_user_account_id');
            $table->string('group_name');
            $table->dateTime('accepted_at')->nullable();
            $table->dateTime('declined_at')->nullable();
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
        Schema::dropIfExists('app_user_account_invitations');
    }
}

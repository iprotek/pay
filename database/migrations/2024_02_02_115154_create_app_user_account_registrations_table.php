<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppUserAccountRegistrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_user_account_registrations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('oauth_client_id');
            $table->string('email');
            $table->string('name');
            $table->dateTime('accepted_at')->nullable();
            $table->dateTime('declined_at')->nullable();
            $table->dateTime('expired_at')->nullable();
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
        Schema::dropIfExists('app_user_account_registrations');
    }
}

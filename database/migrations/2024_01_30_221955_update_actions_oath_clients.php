<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateActionsOathClients extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('oauth_clients', function (Blueprint $table) {
            $table->boolean('disable_api_allow_register_app_user_account')->default(0);
            $table->boolean('disable_api_allow_share')->default(0);
            $table->text('app_settings')->nullable();
            //$table->dateTime('email_verified_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}

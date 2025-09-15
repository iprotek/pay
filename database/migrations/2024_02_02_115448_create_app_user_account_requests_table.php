<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppUserAccountRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_user_account_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('oauth_client_id');
            $table->string('request_email');
            $table->bigInteger('request_group_id');
            $table->bigInteger('request_app_user_id');
            $table->bigInteger('requestor_app_user_id');
            $table->string('requestor_app_user_email');
            $table->text('requestor_message')->nullable();
            $table->bigInteger('requestor_app_user_account_id');
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
        Schema::dropIfExists('app_user_account_requests');
    }
}

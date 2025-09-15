<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateXracUserRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('xrac_user_roles', function (Blueprint $table) {
            $table->bigIncrements('id');
            //DISTINCTION
            $table->string('domain');
            $table->string('local_url');
            $table->bigInteger('app_user_account_id');
            $table->bigInteger('oauth_client_id');
            $table->bigInteger('local_branch_id')->default(1);
            $table->integer('local_system_id')->default(0);
            $table->integer('local_role_id');

            //SETTINGS
            $table->boolean('is_default')->default(1); //1-default 0-custom
            $table->longText('data')->nullable();
            $table->boolean('is_active')->default(1); //ALLOWED


            //APPENDS
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
        Schema::dropIfExists('xrac_settings');
    }
}

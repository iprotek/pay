<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAddedByAppUserAccounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('app_user_accounts', function (Blueprint $table) {
            $table->bigInteger('add_app_user_by')->nullable();
            $table->bigInteger('add_admin_user_by')->nullable();
            $table->string('group_name')->nullable();
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

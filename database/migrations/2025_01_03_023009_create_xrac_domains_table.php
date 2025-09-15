<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateXracDomainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('xrac_domains', function (Blueprint $table) {
            $table->bigIncrements('id');
            //DISTINCTION
            $table->bigInteger('app_user_account_id');
            $table->bigInteger('oauth_client_id');
            //$table->bigInteger('local_branch_id')->default(1);
            $table->integer('local_system_id')->default(0);
            $table->string('local_url');

            $table->string('name');
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
        Schema::dropIfExists('xrac_domains');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateXracRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('xrac_roles', function (Blueprint $table) {
            $table->bigIncrements('id');
            //DISTINCTION
            $table->string('domain');
            $table->bigInteger('oauth_client_id');
            $table->integer('local_role_id');
            $table->string('local_url');

            $table->string('name');
            $table->text('description')->nullable();
            $table->string('is_active')->default(1);
            $table->longText('default_data');

            $table->softDeletes();
            $table->bigInteger('created_pay_user_account_id')->nullable();
            $table->bigInteger('updated_pay_user_account_id')->nullable();
            $table->bigInteger('deleted_pay_user_account_id')->nullable();
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
        Schema::dropIfExists('xrac_roles');
    }
}

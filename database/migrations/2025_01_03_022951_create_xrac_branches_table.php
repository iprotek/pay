<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateXracBranchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('xrac_branches', function (Blueprint $table) {
            $table->bigIncrements('id');
            //DISTINCTION
            $table->string('domain');
            $table->bigInteger('oauth_client_id');
            $table->bigInteger('local_branch_id')->default(1);
            $table->integer('local_system_id')->default(0);
            $table->string('local_url');

            $table->string('name');
            $table->boolean('is_active')->default(1);

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
        Schema::dropIfExists('xrac_branches');
    }
}

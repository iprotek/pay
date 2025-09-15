<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('account_no')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('display_name');
            $table->string('contact_no')->nullable();
            $table->string('email')->unique();
            $table->string('address')->nullable();
            $table->string('company')->nullable();
            $table->bigInteger('created_by');
            $table->bigInteger('updated_by')->nullable();
            $table->bigInteger('deleted_by')->nullable();
            $table->string('password')->nullable();
            $table->dateTime('email_verified_at')->nullable();
            $table->string('remember_token')->nullable();
            $table->dateTime('contact_no_verified_at')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('app_users');
    }
}

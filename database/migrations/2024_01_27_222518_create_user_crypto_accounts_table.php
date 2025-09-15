<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserCryptoAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_crypto_accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('app_user_id');
            $table->string('crypto_address', 255);
            $table->text('private_key')->nullable();
            $table->string('password', 200)->nullable();
            $table->boolean('is_default')->default(1);
            $table->boolean('is_external')->default(0);
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
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
        Schema::dropIfExists('user_crypto_accounts');
    }
}

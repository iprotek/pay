<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstancePermissionTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('instance_permission_tokens', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('guard');
            $table->string('name');
            $table->text('scopes');
            $table->bigInteger('user_admin_id');
            $table->text('session_id');
            $table->longText('access_token');
            $table->dateTime('expires_at');
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
        Schema::dropIfExists('instance_permission_tokens');
    }
}

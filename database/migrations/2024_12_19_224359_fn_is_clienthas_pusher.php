<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FnIsClienthasPusher extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        DB::unprepared("DROP FUNCTION IF EXISTS fnIsClienthasPusher");
        DB::unprepared('
        CREATE FUNCTION `fnIsClienthasPusher`(_oauth_client_id INT) RETURNS tinyint(1)
        BEGIN
            DECLARE HASPUSHER BIGINT DEFAULT 0;
            
            SELECT id INTO HASPUSHER FROM message_sockets WHERE oauth_client_id = _oauth_client_id AND is_active = 1;

            RETURN IF(HASPUSHER>0, 1, 0);
        END
        ');
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

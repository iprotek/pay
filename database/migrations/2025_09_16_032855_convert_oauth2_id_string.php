<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //
        \DB::statement('ALTER TABLE `app_user_accounts` MODIFY `oauth_client_id` VARCHAR(100) NOT NULL');
        \DB::statement('ALTER TABLE `app_user_account_groups` MODIFY `oauth_client_id` VARCHAR(100) NOT NULL');
        \DB::statement('ALTER TABLE `app_user_account_registrations` MODIFY `oauth_client_id` VARCHAR(100) NOT NULL');
        \DB::statement('ALTER TABLE `app_user_account_invitations` MODIFY `oauth_client_id` VARCHAR(100) NOT NULL');
        \DB::statement('ALTER TABLE `app_user_account_requests` MODIFY `oauth_client_id` VARCHAR(100) NOT NULL');
        \DB::statement('ALTER TABLE `message_sockets` MODIFY `oauth_client_id` VARCHAR(100) NOT NULL');
        \DB::statement('ALTER TABLE `xrac_user_roles` MODIFY `oauth_client_id` VARCHAR(100) NOT NULL');
        \DB::statement('ALTER TABLE `xrac_branches` MODIFY `oauth_client_id` VARCHAR(100) NOT NULL');
        \DB::statement('ALTER TABLE `xrac_domains` MODIFY `oauth_client_id` VARCHAR(100) NOT NULL');
        \DB::statement('ALTER TABLE `xrac_roles` MODIFY `oauth_client_id` VARCHAR(100) NOT NULL');
        \DB::statement('ALTER TABLE `xrac_update_logs` MODIFY `oauth_client_id` VARCHAR(100) NOT NULL');


        //UPDATE CLIENT PUSHER
        \DB::unprepared("DROP FUNCTION IF EXISTS fnIsClienthasPusher");
        \DB::unprepared('
        CREATE FUNCTION `fnIsClienthasPusher`(_oauth_client_id VARCHAR(100)) RETURNS tinyint(1)
        BEGIN
            
            IF EXISTS ( SELECT * FROM message_sockets WHERE oauth_client_id = _oauth_client_id AND is_active = 1 ) THEN
				RETURN 1;
            END IF;
            
            RETURN 0;
        END
        ');


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

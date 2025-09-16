<?php

namespace iProtek\Pay\Helpers;

use DB; 
use Illuminate\Support\Facades\Queue;
use iProtek\Pay\Models\AppUser;
use iProtek\Pay\Models\AppUserAccount;
use Laravel\Passport\Client;
use Illuminate\Support\Facades\Schema;

class PassportClientHelper
{
    public static function check($client_id, $email, $is_share = false){
        
        $client = Client::find($client_id);
        //Check if client_id allows sharing and registration.
        if(!$client){
            return ["status"=>0, "message"=>"Application not found"];
        }

        if($client->disable_api_allow_share == 1){
            return ["status"=>0, "message"=>"Sharing workspace is disabled in this application"];
        }
        if($client->disable_api_allow_register_app_user_account == 1 && $is_share){
            //Check if
            $app_user = AppUser::where('email', $email)->first();
            if(!$app_user){
                return ["status"=>0, "message"=>"Application does not allow registration(1)."];
            }
            $app_user_account = AppUserAccount::where(["oauth_client_id"=>$client->id, "app_user_id"=>$app_user->id ])->first();
            if(!$app_user_account){
                return ["status"=>0, "message"=>"Application does not allow registration(2)."];
            }else{
                return ["status"=>1, "message"=>"Proceed Sharing.."];
            }
        }
        else if($client->disable_api_allow_register_app_user_account == 1){
            return ["status"=>0, "message"=>"Application does not allow registration."];
        }

        return["status"=>1, "message"=>"Application Valid"];

    }

    public static function oauth_fields(){

        $user_id_col  = "";
        $redirect_col = "";
        
        //USER ID COLUMN
        if (Schema::hasColumn('oauth_clients', 'user_id')) {
            $user_id_col = 'user_id';
        }
        else if(Schema::hasColumn('oauth_clients', 'owner_id')) {
            $user_id_col = 'owner_id';
        }

        //
        if (Schema::hasColumn('oauth_clients', 'redirect_uris')) {
            $redirect_col = 'redirect_uris';
        }
        else if(Schema::hasColumn('oauth_clients', 'redirect')) {
            $redirect_col = 'redirect';
        }



        return [
            "user_id_column"=>$user_id_col,
            "redirect_column"=>$redirect_col
        ];

    }

}

<?php

namespace iProtek\Pay\Helpers;
use DB;
use Illuminate\Http\Request;
use iProtek\Pay\Models\Auths\AppUser;
use iProtek\Pay\Models\AppUserAccount;
use iProtek\Pay\Models\AppUserAccountGroup;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
//use Illuminate\Database\Events\StatementPrepared;
use iProtek\Pay\Models\UserCryptoAccount;

class AppUserAccountHelper
{ 
    public static function add_app_user_account(Request $request, $client_id, $admin_user_id=0, $add_by_app_user_id = 0, $notify_added = true){

        //Check email if exists on app_user if not then create.
        //$client_id = $request->client_id;
        $email = $request->email;
        $name = $request->name ?: Str::random(20); 

        $app_user = static::add_app_user($email, $admin_user_id, false);
        $app_user_id = $app_user['data_id'];

        //Add to app_user_accounts
        $client_app_user_account = static::add_client_app_user_account($client_id, $app_user_id, $email, $name, $admin_user_id, $add_by_app_user_id, $request->password   );
        $client_app_user_account_id = $client_app_user_account['data_id']; 

        //Email Notification for Added App User
        
        return ["status"=>1, "message"=>"App User Account Added", "data_id"=>$client_app_user_account_id,"app_user_id"=>$app_user_id, "app_user_account_id"=>$client_app_user_account_id ];
    }

    public static function add_app_user($email, $admin_user_id = 0, $notify_email = false){ 

        $app_user = AppUser::where('email', $email)->first();
        if($app_user){
            return ["status"=>1, "message"=>"App User Already Added.", "data_id"=>$app_user->id];
        }
        
        $account_no = Str::random(50);
        $first_name = Str::random(12);
        $last_name = Str::random(12);
        $display_name = $first_name." ".$last_name;
        $contact_no = "N/A";
        $address = "N/A";
        $company = "N/A";
        $password = Hash::make( Str::random(50) ); 
        //$password = Hash::make( "1234" ?: Str::random(50) ); 

        $app_user =  AppUser::create([
            "account_no"=>$account_no,
            "first_name"=>$first_name,
            "last_name"=>$last_name,
            "email"=>$email,
            "display_name"=>$display_name,
            "contact_no"=>$contact_no,
            "address"=>$address,
            "company"=>$company,
            "password"=>$password,
            "created_by"=>$admin_user_id
        ]);

        //Create crypto account if app_user has no crypto account
        static::auto_create_crypto($app_user->id);

        //Notify Email
        if($notify_email){
            

        }

        return ["status"=>1, "message"=>"App User Successfully Added.", "data_id"=>$app_user->id];
    }


    public static function add_client_app_user_account( $client_id, $app_user_id, $email, $name, $user_admin_id = 0, $add_by_app_user_id = 0, $password=""){
        $app_user_account = AppUserAccount::where(['oauth_client_id'=> $client_id, "app_user_id"=> $app_user_id])->first();
        if($app_user_account){
            return ["status"=>1, "message"=>"Already Added.", "data_id"=>$app_user_account->id];
        }

        $app_user_account = AppUserAccount::create([
            "oauth_client_id"=>$client_id,
            "app_user_id"=>$app_user_id,
            "password"=> $password ? Hash::make( $password ) :  Hash::make( Str::random(50) ),
            "is_blocked"=>0,
            "blocked_by"=>0,
            "provider"=>"app_user_account",
            "email"=>$email,
            "name"=> $name ?: Str::random(20),
            "add_by_app_user_id"=>$add_by_app_user_id,
            "add_admin_user_by"=>$user_admin_id
        ]);

        //Create GROUP
        AppUserAccountGroup::create([
            "app_user_id"=>$app_user_id,
            "oauth_client_id"=>$client_id,
            "is_blocked"=>0,
            "accepted_at"=>\Carbon\Carbon::now(),
            "group_id"=>$app_user_account->id,
            "role"=>"owner"
        ]);

        return ["status"=>1, "message"=>"Already Added.", "data_id"=>$app_user_account->id];
    }

    public static function auto_create_crypto($app_user_id){
        /*
        $user_crypto = UserCryptoAccount::where('app_user_id', $app_user_id)->first();
        if($user_crypto){
            return ["status"=>1, "message"=>"Account already added.", "data_id"=>$user_crypto->id];
        }
        $user_crypto = UserCryptoAccount::create([
            "app_user_id"=>$app_user_id,
            "crypto_address"=>""
        ]);

        return ["status"=>1, "message"=>"Account successfully added.", "data_id"=>$user_crypto->id];
        */
        return ["status"=>0, "message"=>"Account creation not available yet.",];

    }

    public static function check($app_user_id){
        $app_user_account = AppUserAccount::find($app_user_id);
        if(!$app_user_account)
            return ["status"=>0, "message"=>"Your app user account invalidated."];
        if($app_user_account->is_blocked == 1)
            return ["status"=>0, "message"=>"User currently blocked."];
        return ["status"=>1 , "message"=> "Valid"];
    }


}


?>
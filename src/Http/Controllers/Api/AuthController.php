<?php

namespace iProtek\Pay\Http\Controllers\Api;

use iProtek\Pay\Http\Controllers\Controller;
use Illuminate\Http\Request;
use iProtek\Pay\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Str;
use iProtek\Pay\Http\Controllers\_Common\_CommonController;
use Illuminate\Support\Facades\Hash;
use iProtek\Pay\Models\Auths\AppUser;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class AuthController extends _CommonController
{
    // 
    public function app_user_register(Request $request){

        $user_id = 0;
        if(auth('admin')->check()){
            $user_id = auth('admin')->user()->id;
        }

        $this->validate($request, [
            "email"=>"required|email"
        ]);

        $email = $request->email;
       $app_user = AppUser::where('email',$email)->first();

       if(!$app_user){

            $account_no = Str::random(50);
            $first_name = $request->first_name ?: Str::random(12);
            $last_name = $request->last_name ?: Str::random(12);
            $display_name = $first_name." ".$last_name;
            $contact_no = $request->contact_no;
            $address = $request->address;
            $company = $request->company;
            //$password = Hash::make( $request->password ?: Str::random(50) ); 
            $password = Hash::make( "1234" ?: Str::random(50) ); 

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
                "created_by"=>$user_id
            ]);
        }

        return ["status"=>1, "message"=>"User Created", "user_id"=>$app_user->id];

    }
    public function app_user_login(Request $request)
    {

        $client = $request->get('client');
        $email = $request->input('email');
        $password = $request->input('password');

        $request = Request::create('/oauth/token', 'POST', [
            'grant_type' => 'password',
            'client_id' => $client->id,
            'client_secret' =>  $client->plain_secret,
            'username' => $request->input('email'),
            'password' => $request->input('password'),
            'scope' => '',
        ]);

        $response = app()->handle($request);
        $result = json_decode($response->getContent(), true);
        
        $status_code = $response->getStatusCode();

        //Intentional return for testing..
        if($status_code != 200){
            
            return response()->json("Something goes wrong. $email and $password on $client->id = $client->plain_secret", $status_code); 
        
        }


        return response()->json($result, $response->getStatusCode()); 
        //return json_decode($response->getContent(), true);

    }

    //create client id and secret for grant_type password? 
    public function createPassportClient()
    {
        $client = new Client();
        
        //$client->user_id
        $client->name = 'Your Client Name'; // This should be the applicaiton name.
        $client->redirect = 'your-callback-url'; // Optional, specify the callback URL for authorization code grant type
        $client->personal_access_client = false; // Set to true if it's a personal access client
        $client->password_client = true; // Set to true if it's a password grant client
        $client->revoked = false; // Set to true to revoke the client
    
        /*IMPORTANT*/
        //provider
        //user_id -> should be the
        //name should be the application name




        $client->save();

        // Access the generated client ID and secret
        $clientId = $client->id;
        $clientSecret = $client->secret;

        return response()->json([
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
        ]);
    }



    public function app_user(Request $request){ 
        $user = $request->user();

        // Now $user contains the authenticated user information
        return response()->json($user);


    }

    public function get_client_info(Request $request){
        //return response()->json(['error' => "This app doesn't allow registration"], 401);
        $client = $request->get('client');

        $clientInfo = \iProtek\Pay\Models\ClientInfo::with(['socket_info', 'apps'])->select(
            'id', 
            'name', 
            'user_id',
            'redirect', 
            'disable_api_allow_register_app_user_account', 
            'disable_api_allow_share',
            'app_settings',
            'created_at',
            'updated_at'
        )->find($client->id);
        
       // $clientInfo->socket_settings = $clientInfo->socket_info->socket_settings;
        
        if($clientInfo->socket_info){ 
            $socket_settings = json_decode(json_encode( $clientInfo->socket_info->socket_settings ));
            if($socket_settings){
                $clientInfo->socket_settings = $socket_settings;
                /*
                $clientInfo->socket_settings = [
                    "is_active"=>$socket_settings->is_active,
                    "name"=>$socket_settings->socket_name,
                    "key"=>$socket_settings->key,
                    "cluster"=>$socket_settings->cluster
                ];
                */
            }
        }
            
        unset($clientInfo->socket_info);


        return $clientInfo;
    }

    public function register_app_user_account(Request $request){
        //Validate Client
        //$client_id = $request->client_id;
        //$client = \DB::table('oauth_clients')->where('client_id', $request->client_id)->where('secret', $request->client_secret)->first();
        
        $client = $request->get('client');
        if($client->disable_api_allow_register_app_user_account == 1){
            return response()->json(['error' => "This app doesn't allow registration"], 401);
        }
        //return response()->json(['error' => "This app doesn't allow registration"], 401);
        
        
        return ["status"=>1, "message"=>"Invalidated", "client"=>$client ];

        //Check email if exists on app_user if not then create.

        //Add to app_user_accounts

        //Create crypto account if app_user has no crypto account

        return [
            "status"=>1,
            ""
        ];

    }

    public function share_group_app_user_account(Request $request){
        //Can share group

        //should allow registration
        //should allow share
        $user = $request->user();


        if(!$user)
            return [];




    }

    public function get_users_by_client(Request $request){

        $client = $request->get('client');
        $user_limit = 0;

        if( isset($client->user_limit) && $client->user_limit){
            $user_limit = $client->user_limit;
        }
        $columns = Schema::getColumnListing('app_user_accounts'); 
       
        $excludedColumns = ['password', 'remember_token', 'provider', 'add_app_user_by', 'add_admin_user_by', 'group_name'];

        $selectedColumns = array_diff($columns, $excludedColumns);

        $app_user_accounts = DB::table('app_user_accounts')->select($selectedColumns);

        if( $request->search && trim($request->search) && $request->exact == "email"){
            $app_user_accounts->where( 'email', trim($request->search) );
        }

        if( $user_limit ){
           return  $app_user_accounts->where('oauth_client_id', $client->id)->limit($user_limit)->get();
        }
        return $app_user_accounts->where('oauth_client_id', $client->id)->get();
    }

    public function get_user_by_client(Request $request, $id){
        $client = $request->get('client'); 
        
        $columns = Schema::getColumnListing('app_user_accounts'); 
       
        $excludedColumns = ['password', 'remember_token', 'provider', 'add_app_user_by', 'add_admin_user_by', 'group_name'];

        $selectedColumns = array_diff($columns, $excludedColumns);
 
        return DB::table('app_user_accounts')->select($selectedColumns)->where('oauth_client_id', $client->id)->find($id);


    }




}

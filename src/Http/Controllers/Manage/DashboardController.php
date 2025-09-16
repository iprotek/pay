<?php

namespace iProtek\Pay\Http\Controllers\Manage;

use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Validator;
use iProtek\Pay\Http\Controllers\_Common\_CommonController;
use Illuminate\Routing\Controller;
use iProtek\Pay\Models\Invoice;
use iProtek\Pay\Models\Item;
use iProtek\Pay\Models\CompanyItem;
use DB;
use Laravel\Passport\Client;
use iProtek\Pay\Models\ClientInfo;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class DashboardController extends _CommonController
{
    //
    public $guard = 'admin';
    public static $OauthFields = null;



    public function __construct()
    {
        $this->middleware('auth');
        static::$OauthFields = \iProtek\Pay\Helpers\PassportClientHelper::oauth_fields();

    }
    public function index(Request $request){

        //$user = auth()->user();
        //$user->can('manage-dashboard');
        return $this->view('manage.dashboard.index');
    }

    public function passport_client_list(Request $request){
        $user_id = auth()->user()->id; 
        $passport_clients = Client::where(static::$OauthFields["user_id_column"], $user_id); 
        $passport_clients->select('*', DB::raw(' fnIsClienthasPusher(id) as has_pusher ' ) );
        $passport_clients->orderBy('name','ASC');
        $passport_clients->orderBy('created_at','DESC');
        return $passport_clients->paginate(10);
    }

    public function get_client(Request $request, Client $id){
        $user_id = auth()->user()->id;
        //return $id;

        if($id->{static::$OauthFields["user_id_column"]} == $user_id){            

            $client_info = ClientInfo::with(['socket_info'])->find($id->id);

            $clientSecret = \DB::table('oauth_clients')->where(static::$OauthFields["user_id_column"], $user_id) ->where('id', $id->id)->first();
            return [ "client"=> $client_info, "bearer"=> base64_encode($clientSecret->id.':'.$clientSecret->secret) ];
        }

        return null;
    }

    public function get_client_secret(Request $request, Client $id){

        $user_id = auth()->user()->id;
        $clientSecret = \DB::table('oauth_clients')->where(static::$OauthFields["user_id_column"], $user_id)
                ->where('id', $id->id)
                ->value('secret');
        if(!$clientSecret){
            return null;
        } 
        return ["client_secret"=>$clientSecret];

    }

    public function add_client(Request $request){
        $this->validate($request,[
            "app_name"=>"required|string|min:8",
            //"redirect"=>"required",
            "socket_settings"=>"required"
        ]);
        
        $user_id = auth()->user()->id;

        $client_exists = Client::where(['name'=> $request->app_name, static::$OauthFields["user_id_column"]=>$user_id, 'provider'=>'app_user_account'])->first();
        if($client_exists){
            return ["status"=>0,"message"=>"App already exists."];
        }


        //
        $socket_settings = json_decode( json_encode( $request->socket_settings) );
        //check if active
        if($socket_settings->is_active){
            //check app_id if exists
            if(!$socket_settings->socket_app_id){
                return ["status"=>0,"message"=>"App Id is required"];
            }
            $exists = \iProtek\Pay\Models\MessageSocket::where('socket_app_id', $socket_settings->socket_app_id)->where('name', $socket_settings->socket_name)->where('is_active', 1)->first();
            if($exists){
                return ["status"=>0, "message"=>"Push Notification is currently in use."];
            }
        }

         
        $client = new Client();
        
        //$client->user_id
        
        $client->{static::$OauthFields["user_id_column"]} = $user_id;
        $client->name = $request->app_name; // This should be the applicaiton name.
        $redirect_column = static::$OauthFields["redirect_column"];
        if( $redirect_column == "redirect_uris"){
            $client->{$redirect_column}  = [$request->redirect]; // Optional, specify the callback URL for authorization code grant type
        }
        else if( $redirect_column == "redirect" ){
            $client->{$redirect_column}  = $request->redirect; // Optional, specify the callback URL for authorization code grant type
        }


        if (Schema::hasColumn('oauth_clients', 'personal_access_client')) {
            $client->personal_access_client = false; // Set to true if it's a personal access client
        }
        
        if (Schema::hasColumn('oauth_clients', 'password_client')) {
            $client->password_client = true; // Set to true if it's a password grant client
        }
        else if(Schema::hasColumn('oauth_clients', 'grant_types')){
            //authorization_code, password, client_credentials, refresh_token
            $client->grant_types = ["password"];
        }




        
        $client->revoked = false; // Set to true to revoke the client
        $client->provider = 'app_user_account';
         
        $clientSecretResult = $this->randString($request);
        if($socket_settings->is_active){
            \iProtek\Pay\Models\MessageSocket::create([
                "user_id"=>$user_id,
                "oauth_client_id"=>$client->id,
                "name"=>$socket_settings->socket_name,
                "is_active"=>$socket_settings->is_active,
                "socket_app_id"=>$socket_settings->app_id,
                "socket_settings"=>$socket_settings
            ]);
        }
        
        // Ensure that the clientSecret contains at least one uppercase and one lowercase character
         
        $client->secret =  $clientSecretResult;
        /*IMPORTANT*/
        $client->save();

        return ["status"=>1,"message"=>"Successfully Added","data_id"=>$client->id];


    }

    public function update_client(Request $request, Client $id){
        
        $this->validate($request,[
            "app_name"=>"required|string|min:8",
            "redirect"=>"required"
        ]);
        $user_id = auth()->user()->id;


        $socket_settings = json_decode( json_encode( $request->socket_settings) );

        //Check messagesocket conflict
        if($socket_settings && $socket_settings->is_active){
            //check app_id if exists
            if(!$socket_settings->app_id){
                return ["status"=>0,"message"=>"App Id is required"];
            }
            $exists = \iProtek\Pay\Models\MessageSocket::whereRaw( " oauth_client_id <> ? and is_active=1 " ,[$id->id])->where('socket_app_id', $socket_settings->app_id)->where('name', $socket_settings->socket_name)->where('is_active', 1)->first();
            if($exists){
                $client =  Client::find($exists->id);
                if($client)
                    return ["status"=>0, "message"=>"Push Notification is currently in use by: [$exists->id] - ".$client->name];
                
                return ["status"=>0, "message"=>"Something goes wrong."];
            }
        }

        $redirect_column = static::$OauthFields["redirect_column"];
        if($id->{static::$OauthFields["user_id_column"]} == $user_id){
            \DB::table('oauth_clients')->where('id','=', $id->id)->update([ 
                "name"=>$request->app_name,
                $redirect_column => ($redirect_column == 'redirect_uris' ? [$request->redirect] : $request->redirect),
                "revoked"=>$request->revoked,
                "disable_api_allow_register_app_user_account"=>$request->disable_api_allow_register_app_user_account,
                "disable_api_allow_share"=>$request->disable_api_allow_share,
                "updated_at"=>\Carbon\Carbon::now()
            ]);
            
            $socket = \iProtek\Pay\Models\MessageSocket::where('oauth_client_id', $id->id)->first();
            if($socket_settings && !$socket){

                //Validate here 
                \iProtek\Pay\Models\MessageSocket::create([
                    "user_id"=>$user_id,
                    "oauth_client_id"=>$id->id,
                    "name"=>$socket_settings->socket_name,
                    "is_active"=>$socket_settings->is_active,
                    "socket_app_id"=>$socket_settings->app_id,
                    "socket_settings"=>$socket_settings
                ]);

            }else if( $socket_settings ){
                $socket->name = $socket_settings->socket_name;
                $socket->is_active = $socket_settings->is_active;
                $socket->socket_app_id = $socket_settings->app_id;
                $socket->socket_settings = $socket_settings;
                $socket->save();
            }

            return ["status"=>1,"message"=>"Client Updated","data_id"=>$id->id];
        }
        return ["Status"=>0, "message"=>"Unauthorized"];
    }


    public function randString(Request $request){
        
        $rand_number = mt_rand(1, 10);
        //return $rand_number;
        $counter = 0;
        do {
            $clientSecret = Str::random(40); // You can adjust the length as needed
            $digit_count = strlen(preg_replace('/[^0-9]/', '', $clientSecret));
            $counter++;

        } while ( ($digit_count < (10 + $rand_number) || $digit_count > 25) && $counter < 20 );
        //$randomString = Str::random(40);

        
        $clientSecretResult = "";
        foreach(str_split($clientSecret) as $char){
            $is_capital = mt_rand(1, 20) % 2;
            if($is_capital == 1){
               $char = strtoupper($char);
            }
            $clientSecretResult .= $char;
        }
        $clientSecretResult = substr(str_shuffle( $clientSecretResult ), 0, 40);



        return $clientSecret;
    }

    public function renewSecreteClient(Request $request, Client $id){
        
        $user_id = auth()->user()->id;

        if($id->user_id == $user_id){
            $client_secret = $this->randString($request);
            \DB::table('oauth_clients')->where('id','=', $id->id)->update([
                "secret"=> $client_secret,
                "updated_at"=>\Carbon\Carbon::now()
            ]);
         
            return ["status"=>1, "message"=>"Secret updated.", "client_secret"=>$client_secret, "bearer"=>base64_encode($id->id.':'.$client_secret) ];
        }

        return ["status"=>0, "message"=>"Invalidated", "client_secret"=>null,"bearer"=>null];

    }
     

}

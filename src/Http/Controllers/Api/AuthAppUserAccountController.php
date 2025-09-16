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
use iProtek\Pay\Models\AppUserAccount; 
use iProtek\Pay\Models\AppUserAccountGroup; 
use Illuminate\Support\Facades\Log;

class AuthAppUserAccountController extends _CommonController
{

    public function list(Request $request){
        $user = $request->user();
        if(!$user){
            return response()->json(["message"=>"User invalidated!"], 403);
        } 

        $clients_req = Client::where('id',  $user->oauth_client_id);
        $clients = $clients_req->get(); 

        $client_ids = [];
        foreach($clients as $client){
            $client_ids[] = $client->id;
        }
        
        
        $user_accounts = \DB::table('app_user_accounts')
            ->whereIn('oauth_client_id', $client_ids)
            ->select('id', 'oauth_client_id', 'is_blocked', 'blocked_by', 'name', 'email', 'add_admin_user_by', 'add_app_user_by');
        if($request->search){
            $search = '%'.str_replace(' ', '%', $request->search).'%';
            $user_accounts->whereRaw('CONCAT(name, email) LIKE ?', [$search]);
        }

        return $user_accounts->paginate(10);
    }

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

        Log::error("GG".$client->plain_secret);

        $request = Request::create('/oauth/token', 'POST', [
            'grant_type' => 'password',
            'client_id' => $client->id,
            'client_secret' =>   $client->secret,
            'username' => $request->input('email'),
            'password' => $request->input('password'),
            'scope' => '',
        ]);

        $response = app()->handle($request);

        return json_decode($response->getContent(), true);

    } 

    public function app_user(Request $request){ 
        $user = $request->user();

        // Now $user contains the authenticated user information
        return response()->json($user);


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


    }

    public function share_group_app_user_account(Request $request){
        //Can share group

        //should allow registration
        //should allow share
        $user = $request->user();


        if(!$user)
            return [];




    }

    public function update_password(Request $request){
        $this->validate($request, [
            "current_password"=>"required",
            "password"=>['required', 'string', 'min:5', 'confirmed']
        ]);
        
        $app_user_account = AppUserAccount::find($request->user()->id );
        //Check if new password is still the same.
        if($app_user_account->isPasswordCorrect($request->password)){
            return [ "status"=>0, "message"=>"Invalid. Password still the same.", "data_id"=>$app_user_account->id ];
        }


        if($app_user_account->isPasswordCorrect($request->current_password)){
            
            
            $app_user_account->updatePassword($request->password);
            $app_user_account->save();
            //Logout the token associated on this account.
            return ["status"=>1,"message"=>"Password Successfully Updated", "data_id"=>$app_user_account->id];
        }
        return ["status"=>0, "message"=>"Wrong Password", "data_id"=>0];
    }

    public function logout(Request $request){
        $user = $request->user();

        // Revoke the current access token
        $user->token()->revoke();

        return ["status"=>1,"message"=>"You are now log-out", "data_id"=>$user->id];
    }
    public function logout_all(Request $request){
        $user = $request->user();
        // Revoke the current access token
        $user->tokens->each(function ($token, $key) {
            $token->revoke();
        });
        return ["status"=>1,"message"=>"You are now log out to all devices.", "data_id"=>$user->id];
    }

    public function update_my_name(Request $request){
        $this->validate($request, [
            "name"=>"required|min:3"
        ]);
        
        $user = $request->user();
        $app_user_account = AppUserAccount::find($user->id);
        $app_user_account->name = $request->name;
        $app_user_account->save();

        return ["status"=>1, "message"=>"Name updated.", "data_id"=>$app_user_account->id ];
    }

    public function update_group_name(Request $request){

        $this->validate($request, [
            "group_name"=>"required|min:3"
        ]);
        
        $user = $request->user();
        $app_user_account = AppUserAccount::find($user->id);
        $app_user_account->group_name = $request->group_name;
        $app_user_account->save();

        return ["status"=>1, "message"=>"Name updated.", "data_id"=>$app_user_account->id ];

    }

    public function update_pseudo_email(Request $request){
        
        $this->validate($request, [
            "email"=>"required|email",
            "password"=>"required"
        ]);
        
        $user = $request->user();
        $app_user_account = AppUserAccount::find($user->id);
        
        if($app_user_account->isPasswordCorrect($request->password)){
            $app_user_account->email = $request->email;
            $app_user_account->save();
            return ["status"=>1, "message"=>"Pseudo email has been set", "data_id"=>$app_user_account->id ];
        }

        return ["status"=>0, "message"=>"Wrong password", "data_id"=>$app_user_account->id ];

    }

    public function allowed_group(Request $request){
        $this->validate($request, [
            "group_id"=>"required"
        ]);


    }

    
    public function share_group_list(Request $request){
        
        $user = $request->user();

        $user_id = $user->id;

        $search_text = str_replace(' ','%', $request->search?:"");
        $with_owner = $request->with_owner;
        $oauth_client_id = $user->oauth_client_id;


        $app_user_account_groups = AppUserAccountGroup::with(['owner','share_user'])
        ->where('group_id', $user_id)->where('oauth_client_id', $oauth_client_id);

        if($with_owner)
            $app_user_account_groups->whereRaw(" (share_to_app_user_account_id = 0  OR share_to_app_user_account_id IN ( SELECT id FROM app_user_accounts WHERE oauth_client_id = ? AND CONCAT(email, name) LIKE CONCAT('%',?,'%')) ) ",[$oauth_client_id, $search_text]);
        else
            $app_user_account_groups->whereRaw(" ( share_to_app_user_account_id IN ( SELECT id FROM app_user_accounts WHERE oauth_client_id = ? AND CONCAT(email, name) LIKE CONCAT('%',?,'%')) ) ",[$oauth_client_id, $search_text]);
        
        return $app_user_account_groups->paginate(10);

    }

}

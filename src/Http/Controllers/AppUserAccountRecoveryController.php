<?php

namespace iProtek\Pay\Http\Controllers;

use Illuminate\Http\Request;
use iProtek\Pay\Models\AppUser;
use iProtek\Pay\Models\AppUserAccount;
use iProtek\Pay\Http\Controllers\_Common\_CommonController;
use Illuminate\Support\Facades\Hash;
use iProtek\Pay\Models\ClientInfo;

class AppUserAccountRecoveryController extends _CommonController
{
    //
    public function send_recovery(Request $request){
        
        $client = $request->get('client');
        
        
        $this->validate($request, [
            "email"=>"required|email"
        ]);

        
        $email = $request->email;
        $app_user = AppUser::where('email', $email)->first();
        if(!$app_user)
            return ["status"=>0, "message"=>"Not registered." ];

        $app_user_account = AppUserAccount::where(["app_user_id"=>$app_user->id, "oauth_client_id"=>$client->id])->first();
        if(!$app_user_account){
            return ["status"=>0, "message"=>"Not registered." ];
        }
        if($app_user_account->is_blocked == 1)
            return ["status"=>0, "message"=>"Email was blocked to use this application."];

        $app_user_account->sendRecoveryLink();

        return ["status"=>1, "message"=>"Email recovery has been sent"];
    }



    public function post_app_user_account_recovery(Request $request){

        if($request->action_button == "REJECT"){
            $request->hasValidSignature(false);
            abort(403, "Rejected");
        }
        
        $is_valid =  $this->validator($request, [ 
            "password"=>"required|confirmed|min:5"
        ]);
        
        if(!$is_valid){
            return redirect()->back();
        }
        //
        $app_user_account_id = $request->app_user_account_id;
        $app_user_account  = AppUserAccount::find($app_user_account_id);

        if(!$app_user_account){
            abort(403, "Account Not Found");
        }

        if($app_user_account->is_blocked == 1){
            abort(403,"Account was blocked to use this app." );
        } 
        $app_user_account->password = Hash::make($request->password);
        $app_user_account->save();


        return $this->get_app_user_account_recovery($request, true);
    }


    public function get_app_user_account_recovery(Request $request, $successfully_reset = false){
        $app_user_account_id =  $request->app_user_account_id;
        $app_user_account  = AppUserAccount::find($app_user_account_id);
        $redirect_url = $request->redirect_url;
        if(!$app_user_account){
            abort(403, "User not found");
        }

        if($app_user_account->is_blocked == 1){
            abort(403, "Account blocked");
        } 

        $client = ClientInfo::find($app_user_account->oauth_client_id);

        return view('app-user-account.recovery-form', [ 
            "client"=>$client,
            "redirect_url"=>$redirect_url, 
            "app_user_account"=>$app_user_account, 
            "successfully_reset"=>$successfully_reset 
        ]);
   
    }
}

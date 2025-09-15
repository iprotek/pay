<?php

namespace iProtek\Pay\Http\Controllers;

use Illuminate\Http\Request;
use iProtek\Pay\Models\AppUserAccountRegistration;
use iProtek\Pay\Models\AppUserAccount;
use iProtek\Pay\Models\AppUser;
use iProtek\Pay\Http\Controllers\_Common\_CommonController;
use Laravel\Passport\Client;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use iProtek\Pay\Helpers\AppUserAccountHelper;
use Illuminate\Support\Facades\Hash;

class AppUserAccountRegistrationController extends _CommonController
{
    //
    public function send_registration(Request $request){


        //return ["status"]
        //return $request->get('client');
        $client = $request->get('client');
        
        if($client->disable_api_allow_register_app_user_account == 1){
            return ["status"=>0, "message"=>"Application Registration is forbidden to this app."];
        }

        $this->validate($request, [
            "email"=>"required|email",
            "name"=>"required"
        ]);

       
        $registration = AppUserAccountRegistration::where(["oauth_client_id"=>$client->id, "email"=>$request->email])->whereRaw(' created_at > DATE_ADD(NOW(), INTERVAL -1 MINUTE) ')->first();
        if($registration){
            return ["status"=>0, "message"=>"Application Registration confirmation link has already been sent to your email. Please retry again after 60 seconds."]; 
        }

        //Check app user id if user already exists.
       $app_user =  AppUser::where('email', $request->email)->first();
       if($app_user){

            $app_user_account = AppUserAccount::where('oauth_client_id', $client->id)->where('app_user_id', $app_user->id)->first();
            if($app_user_account){
                if($app_user_account->is_blocked == 1){
                    return ["status"=>0, "message"=>"You are blocked on using this application. Please contact the developer."];
                }
                return ["status"=>0, "message"=>"Already exists. You may use recovery to reset your account password."];
            }
            //
       }
        //URL::temporarySignedRoute('apps.view-license', $expiration, ['id' => $appLicense->id]);
        
       $created =  AppUserAccountRegistration::create([
            "oauth_client_id"=>$client->id,
            "email"=>$request->email,
            "name"=>$request->name
        ]);

        //$temporaryViewLicenseURL =   URL::temporarySignedRoute('apps.view-license', $expiration, ['id' => $appLicense->id]);
            
        //return redirect($temporaryViewLicenseURL);
        //Send Email
        //MailHelper::send($request->email, new AppUserAccountRegistrationNotification2());
        $created->notifyRegistration();


        return ["status"=>1, "message"=>"Invitation has been sent to your email."];


    }


    public function get_app_user_account_registration(Request $request){

        $app_user_account_registration_id = $request->app_user_account_registration_id;
        $email = $request->email;
        $client_id = $request->client_id;

        $app_user_registration = AppUserAccountRegistration::find($app_user_account_registration_id);
        if(!$app_user_registration){
            abort( 403, 'Forbidden Access'); 
        } 

        if( $app_user_registration->accepted_at || $app_user_registration->declined_at )
            return view('app-user-account.registration-form', ["email"=>$email, "app_user_registration"=>$app_user_registration , "registration_name"=>$app_user_registration->name]);
    
        //Checking if user already exists.
        $app_user = AppUser::where('email', $request->email)->first();
        if($app_user){
            $app_user_account = AppUserAccount::where(['app_user_id'=> $app_user->id, 'oauth_client_id'=>$client_id])->first();
            if($app_user_account){
                if($app_user_account->is_blocked == 1)
                    abort(403, 'Account currently blocked!');
                else
                    abort(403, 'User Already Exists');
            }
        }

       return view('app-user-account.registration-form', ["email"=>$email, "app_user_registration"=>$app_user_registration , "registration_name"=>$app_user_registration->name]);
    }

    public function post_app_user_account_registration(Request $request){ 

        $app_user_registration = AppUserAccountRegistration::find($request->app_user_account_registration_id);
        if( trim($request->action_button) == "DECLINE" ){            
            $app_user_registration->declined_at = \Carbon\Carbon::now();
            $app_user_registration->save();
            return redirect()->back();
        }

        $is_valid =  $this->validator($request, [
            "name"=>"required|min:4",
            "password"=>"required|confirmed|min:5"
        ]);

        if(!$is_valid){
            return redirect()->back();
        }

        
        $app_user_account_registration_id = $request->app_user_account_registration_id;
        $email = $request->email;
        $client_id = $request->client_id;

        //Check If no account yet.
        $app_user = AppUser::where('email', $request->email)->first();
        if($app_user){
            $app_user_account = AppUserAccount::where(['app_user_id'=> $app_user->id, 'oauth_client_id'=>$client_id])->first();
            if($app_user_account){
                if($app_user_account->is_blocked == 1)
                    abort(403, 'Account Current blocked');
                else
                    abort(403, 'Account Already Exists.');
            }
        }


        $result = AppUserAccountHelper::add_app_user_account($request, $client_id, 0, 0, false);
        $app_user_account =  AppUserAccount::find( $result['app_user_account_id'] );
        //Update based on password.
        $app_user_account->name = $request->name;
        $app_user_account->password = Hash::make($request->password);
        $app_user_account->save();

        //Added
        $app_user_registration->accepted_at =  \Carbon\Carbon::now();
        $app_user_registration->save();


        return $this->get_app_user_account_registration($request);
        //return view('app-user-account.registration-form', ["email"=>$email,"registration_name"=>""]);
    }


}

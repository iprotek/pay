<?php

namespace iProtek\Pay\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL; 
use iProtek\Pay\Models\AppUserAccountInvitation;
use iProtek\Pay\Models\AppUser;
use iProtek\Pay\Models\AppUserAccount;
use iProtek\Pay\Models\AppUserAccountGroup;
use iProtek\Pay\Http\Controllers\_Common\_CommonController;
use Laravel\Passport\Client;

class AppUserAccountInvitationController extends _CommonController
{
    //
    public function send_invitation(Request $request){
        
        $user = $request->user();
        $client_id = $user->oauth_client_id;

        $this->validate($request,[
            "email"=>"required|email",
            "role"=>"required"
        ]);

        
        $client = Client::find($client_id);
        $app_invitation = AppUserAccountInvitation::where('email', $request->email)->where('oauth_client_id', $client->id)->whereRaw(' created_at > DATE_ADD(NOW(), INTERVAL -1 MINUTE) ')->first();
        if($app_invitation){
            return ["status"=>0, "message"=>"Invitation already sent. Please retry after 60 seconds."];
        }

        $check = \iProtek\Pay\Helpers\PassportClientHelper::check($client_id, $request->email, true);
        if($check['status'] == 0){
            return $check;
        }

        $app_user_check = \iProtek\Pay\Helpers\AppUserAccountHelper::check($user->id);
        if($app_user_check["status"] == 0){
            return $app_user_check;
        }

        $app_user = AppUser::where(["email"=> $request->email])->first();

        $app_user_account =  AppUserAccount::find($user->id);

        if($app_user){
            if($app_user->id == $app_user_account->app_user_id){
                return ["status"=>0, "message"=>"You cannot Invite yourself."];
            }
        }
        if(!$app_user_account){
            return ["status"=>0, "message"=>"App user account not found.".$user->id];
        }
        else if(!$app_user_account->isPasswordCorrect($request->password)){
            return ["status"=>0, "message"=>"Wrong password."];
        }

        //Check if user already exists
        if($app_user && $app_user_account){
            $app_user_account_group = AppUserAccountGroup::where(["app_user_id"=>$app_user->id, "oauth_client_id"=>$client_id, "group_id"=>$app_user_account->id ])->first();
            if($app_user_account_group){
                if($app_user_account_group->is_blocked == 1){
                    return ["status"=>0, "message"=>"Already exists and blocked"];
                }
                return ["status"=>0, "message"=>"Already exists"];
            }
        }


        $invitation = AppUserAccountInvitation::create([
            "app_user_id" => $user->app_user_id,
            "oauth_client_id" => $client->id,
            "app_name" => $client->name,
            "email" =>$request->email,
            "group_id" => $app_user_account->id,
            "app_user_account_id" => $app_user_account->id,
            "group_name" =>  $app_user_account->group_name ?: "Personal Workspace",
            "role"=>$request->role,
        ]);
        $invitation->notifyInvitation();

        return ["status"=>1, "message"=>"Invitation has been sent."];  
        
    }

    public function post_app_user_account_invitation(Request $request){ 
        
        $app_user_invitation = AppUserAccountInvitation::find($request->app_user_account_invitation_id);
        if( trim($request->action_button) == "DECLINE" ){            
            $app_user_invitation->declined_at = \Carbon\Carbon::now();
            $app_user_invitation->save();
            return redirect()->back();
        }

        $client_id = $app_user_invitation->oauth_client_id;
        $check = \iProtek\Pay\Helpers\PassportClientHelper::check($client_id, $request->email, true);
        if($check['status'] == 0){
            return abort(403, $check['message']);
        }
        

        $has_account =false;
        $group_id = $app_user_invitation->group_id;
        $email = $app_user_invitation->email;
        
        $app_user = AppUser::where(["email"=>$email])->first();
        if($app_user){
            $app_user_account = AppUserAccount::where(["app_user_id"=>$app_user->id, "oauth_client_id"=>$client_id])->first();
            if(  $app_user_account ){
                $has_account = true;
                if($app_user_account->is_blocked == 1){
                    abort(403, 'You are blocked');
                }
            }

            $account_group = AppUserAccountGroup::where(["app_user_id"=>$app_user->id, "oauth_client_id"=>$client_id, "group_id"=>$group_id])->first();
            if($account_group){
                    if($account_group->is_blocked == 1)
                        abort(403, 'You are blocked.');
                abort(403, 'You are already member.');
            }
        }

        if($has_account == false){

            $is_valid =  $this->validator($request, [
                "name"=>"required|min:4",
                "password"=>"required|confirmed|min:5"
            ]);

        }else{

            $is_valid =  $this->validator($request, [
                "name"=>"required|min:4"
            ]);

        }
        if(!$is_valid){
            return redirect()->back();
        }
        //

        $app_user_account = \iProtek\Pay\Helpers\AppUserAccountHelper::add_app_user_account( $request, $client_id, 0, 0, false);
        $app_user_account_id = $app_user_account['app_user_account_id'];
        $app_user_id = $app_user_account['app_user_id'];
        $app_group = AppUserAccountGroup::where(['app_user_id'=>$app_user_id, 'oauth_client_id'=>$client_id, 'group_id'=>$group_id])->first();
        
        if(!$has_account){
            $app_user_account = AppUserAccount::find($app_user_account_id);
            if($app_user_account){
                $app_user_account->name = $request->name;
                $app_user_account->save();
            }

        }
        if($app_group == false){
            AppUserAccountGroup::create([
                "app_user_id"=>$app_user_id,
                "oauth_client_id"=>$client_id,
                "accepted_at"=>\Carbon\Carbon::now(),
                "group_id" => $group_id,
                "is_blocked"=>0,
                "share_to_app_user_account_id"=> $app_user_account_id,
                "role"=>$app_user_invitation->role
            ]);
        }
        else{
            abort(403, 'Invalidated');
        }

        $app_user_invitation->accepted_at = \Carbon\Carbon::now();
        $app_user_invitation->save();

        return $this->get_app_user_account_invitation($request);

    }

    public function get_app_user_account_invitation(Request $request){
 
        $email = $request->email;
        $app_user_invitation = AppUserAccountInvitation::find($request->app_user_account_invitation_id);
       




        $client_id = $app_user_invitation->oauth_client_id;
        $client = Client::find($client_id);
        $check = \iProtek\Pay\Helpers\PassportClientHelper::check($client_id, $request->email, true);
        if($check['status'] == 0){
            return abort(403, $check['message']);
        }

        //Check group id
        $owner = AppUserAccount::find($app_user_invitation->group_id);
        if(!$owner){
            abort(403, 'Workspace not found.');
        }

        if($owner->is_blocked == 1){
            return abort(403, 'Workspace was blocked.');
        }

        $has_app_user_account = false;
        $invitee_account_name = "";

        if( $app_user_invitation->accepted_at || $app_user_invitation->declined_at )
            return view('app-user-account.invitation-form', ["invitee_account_name"=>$invitee_account_name, "owner"=>$owner, "email"=>$email, "app_user_invitation"=>$app_user_invitation, "has_app_user_account"=>false]);




        $app_user = AppUser::where('email', $email)->first();
        if($app_user){
            $invitee_account_name = $app_user->display_name;
            $app_user_account = AppUserAccount::where(['app_user_id'=>$app_user->id, 'oauth_client_id'=>$client_id])->first();
            if($app_user_account){
                $invitee_account_name = $app_user_account->name;
                if($app_user_account->is_blocked == 1){
                    abort(403, 'Your account is currently blocked on this application');
                }
                $joined = AppUserAccountGroup::where(["oauth_client_id"=>$client_id, "app_user_id"=>$app_user->id, "group_id"=>$app_user_invitation->id])->first();
                if($joined){
                    if($joined->is_blocked == 1)
                        abort(403, 'You are blocked');
                    abort(403, 'Already Joined');
                }
                $has_app_user_account = true;
            }

        }


        




        return view('app-user-account.invitation-form', [ "invitee_account_name"=>$invitee_account_name, "owner"=>$owner, "email"=>$email, "app_user_invitation"=>$app_user_invitation, "has_app_user_account"=>$has_app_user_account ]);
    }
   


}

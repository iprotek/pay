<?php

namespace iProtek\Pay\Http\Controllers\Manage;

use Illuminate\Http\Request;
use iProtek\Pay\Http\Controllers\_Common\_CommonController;
use iProtek\Pay\Models\AppUserAccountGroup;
use iProtek\Pay\Models\ClientInfo;

class AppUserAccountGroupController extends _CommonController
{
    //
    public function group_access(Request $request, AppUserAccountGroup $group_access_id ){
        $user = $request->user();
        if(!($user->app_user_id == $group_access_id->app_user_id && $user->oauth_client_id == $group_access_id->oauth_client_id ) )
        {
            abort(404, 'Not Found');
            //abort(403, 'Error '.$user->app_user_id."-".$group_access_id->app_user_id."-". $user->oauth_client_id ."-".$group_access_id->oauth_client_id);
        }

        //You are not blocked in your own group
        if($user->id != $group_access_id->group_id){
            if($group_access_id->is_blocked == 1){
                abort(403, 'You are blocked');
            }
        }
        $app_group = AppUserAccountGroup::with('owner')->find($group_access_id->id);
        //$client_info = ClientInfo::with('apps')->select('id', 'user_id', 'name')->find($user->oauth_client_id);

        $requestor_url = $request->header('SOURCE-URL');
        $system_id = $request->header('SYSTEM-ID');
        $cid = $request->header('CLIENT-ID');
        $sysType = $request->header('SOURCE-TYPE');
        $appUserAccountID = $request->header('PAY-USER-ACCOUNT-ID');
        return [
            "app_user_account"=>$user, 
            "group_access"=>$app_group, 
            "group_id"=>$app_group->group_id, 
            "client_info"=>$request->get('requestor_info')
        ];
    }
}

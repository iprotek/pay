<?php

namespace iProtek\Pay\Http\Controllers\Manage;

use Illuminate\Http\Request;
use iProtek\Pay\Http\Controllers\_Common\_CommonController;
use iProtek\Pay\Models\AppNotification;

class AppNotificationController extends _CommonController
{
    //
    public $guard = 'admin';
    public function add(Request $request){

        $reqData =  $this->validate($request, [
            "local_branch_id"=>"required|integer",
            "local_system_name"=>"nullable"
        ])->validated();

        $reqData["domain"] = parse_url($request->header('SOURCE-URL'), PHP_URL_HOST);// $request->header('SOURCE-URL');
        $reqData["pay_account_id"] = $request->user()->id;
        $reqData["oauth_client_id"] = $request->header('CLIENT-ID');

        $appNotif = AppNotification::where($reqData)->first();

        if(!$appNotif){
            $reqData["notice_count"] = 1;
            AppNotification::create($reqData);
        }
        else{
            $appNotif->notice_count = ( $appNotif->notice_count * 1) + 1;
            $appNotif->save();
        }

        return ["status"=>1, "message"=>"Notice updated."];

    }

    public function get(Request $request){
        
        $reqData =  $this->validate($request, [
            "local_branch_id"=>"required|integer",
            "local_system_name"=>"nullable"
        ])->validated();

        $reqData["domain"] = parse_url($request->header('SOURCE-URL'), PHP_URL_HOST);
        $reqData["pay_account_id"] = $request->user()->id;
        $reqData["oauth_client_id"] = $request->header('CLIENT-ID');

        $appNotif = AppNotification::where($reqData)->first();

        return ["status"=>1, "message"=>"Rendered data", "data"=>$appNotif ];
    }

    public function clear(Request $request){
        
        $reqData =  $this->validate($request, [
            "local_branch_id"=>"required|integer",
            "local_system_name"=>"nullable"
        ])->validated();

        $reqData["domain"] = parse_url($request->header('SOURCE-URL'), PHP_URL_HOST);
        $reqData["pay_account_id"] = $request->user()->id;
        $reqData["oauth_client_id"] = $request->header('CLIENT-ID');

        $appNotif = AppNotification::where($reqData)->whereRaw('notice_count > 0')->first();
        if($appNotif){
            $appNotif->notice_count = 0;
            $appNotif->save();
        }

        return ["status"=>1, "message"=>"Cleared" ];
    }

}

<?php

namespace iProtek\Pay\Http\Controllers\Api;

use Illuminate\Http\Request;
use iProtek\Pay\Http\Controllers\_Common\_CommonController;
use iProtek\Pay\Models\XracRole;

class XracRoleController extends _CommonController
{
    //
    public $guard = "admin";

    public function list(Request $request){

        $roles =  XracRole::withTrashed();

        
        $header_client_id = trim( $request->header('CLIENT-ID') ?: "" );
        //$header_client_secret = trim( $request->header('SECRET') ?: "");
        //$header_pay_url = trim( $request->header('PAY-URL') ?: "");
        $header_source_name = trim( $request->header( 'SOURCE-NAME' ));
        $header_domain = trim( $request->header( 'REQUESTOR-DOMAIN' ) ?: 0);
        $header_system_id = trim( $request->header( 'SYSTEM-ID' ) ?: 0);
        $header_role_id = trim( $request->header( 'role-ID' ) ?: 1);
        $header_source_url = trim( $request->header( 'SOURCE-URL' ));
        $header_user_account_id = trim( $request->header('PAY-USER-ACCOUNT-ID') ?: 0 );
        $pay_proxy_id = trim($request->header('PAY-PROXY-ID'));

        //FILTER
        $roles->where([
            "oauth_client_id"=>$header_client_id,
            "local_url"=>$header_source_url,
            "domain"=>$header_domain,
            //"local_role_id"=>$header_role_id,
            //"local_system_id"=>$header_system_id,
        ]);



        if($request->is_all){
            return $roles->get();
        }


        return $roles->paginate(10);
    }

    public function add_update(Request $request){

        //CHECK IF EXISTS
        $xrac_role = null;

        $header_client_id = trim( $request->header('CLIENT-ID') ?: "" );
        //$header_client_secret = trim( $request->header('SECRET') ?: "");
        //$header_pay_url = trim( $request->header('PAY-URL') ?: "");
        $header_source_name = trim( $request->header( 'SOURCE-NAME' ));
        $header_domain = trim( $request->header( 'REQUESTOR-DOMAIN' ) ?: 0);
        $header_system_id = trim( $request->header( 'SYSTEM-ID' ) ?: 0);
        $header_role_id = trim( $request->header( 'role-ID' ) ?: 1);
        $header_source_url = trim( $request->header( 'SOURCE-URL' ));
        $header_user_account_id = trim( $request->header('PAY-USER-ACCOUNT-ID') ?: 0 );
        $pay_proxy_id = trim($request->header('PAY-PROXY-ID'));

        $this->validate($request, [
            "role_id"=>"required",
            "name"=>"required"
        ]);


        $xrac_role = XracRole::where([
            "domain"=>$header_domain,
            "oauth_client_id"=>$header_client_id,
            "local_role_id"=>$request->role_id,
            //"local_system_id"=>$header_system_id
        ])->withTrashed()->first();

        //UPDATE
        if($xrac_role){
            //$xrac_role->local_system_id = $header_system_id;
            $xrac_role->local_url = $header_source_url;
            $xrac_role->is_active = $request->is_active || $request->is_active == 1 ? true : false;
            
            $xrac_role->name = $request->name;
            $xrac_role->deleted_at = $request->deleted_at;
            $xrac_role->description = $request->description;
            if($request->default_data)
                $xrac_role->default_data = $request->default_data;

            if($request->deleted_at){
                $xrac_role->deleted_pay_user_account_id = $header_user_account_id;
            }else{
                $xrac_role->updated_pay_user_account_id = $header_user_account_id;
            }
            $xrac_role->save();

            return ["status"=>1, "message"=>"Update Details", "data"=>$xrac_role];
        }

        //ADD
        $xrac_role = XracRole::create([
            "domain"=>$header_domain,
            "oauth_client_id"=>$header_client_id,
            "local_role_id"=>$request->role_id,
            "local_system_id"=>$header_system_id,
            "local_url"=>$header_source_url,
            "name"=>$request->name,
            "created_pay_user_account_id"=>$header_user_account_id,

            "is_active"=>$request->is_active || $request->is_active == 1 ? true : false,
            "deleted_at"=>$request->deleted_at,
            "description"=>$request->description,
            "default_data"=>$request->default_data ? $request->default_data : ["_a"=>"nothing_available"]
        ]);


        return ["status"=>1, "message"=>"Successfully Added", "data"=>$xrac_role];



    }
}

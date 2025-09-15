<?php

namespace iProtek\Pay\Http\Controllers\Api;

use Illuminate\Http\Request;
use iProtek\Pay\Http\Controllers\_Common\_CommonController;
use iProtek\Pay\Models\XracBranch;

class XracBranchController extends _CommonController
{
    //
    public $guard = "admin";

    public function list(Request $request){

        $branches =  XracBranch::withTrashed();

        
        $header_client_id = trim( $request->header('CLIENT-ID') ?: "" );
        //$header_client_secret = trim( $request->header('SECRET') ?: "");
        //$header_pay_url = trim( $request->header('PAY-URL') ?: "");
        $header_source_name = trim( $request->header( 'SOURCE-NAME' ));
        $header_domain = trim( $request->header( 'REQUESTOR-DOMAIN' ) ?: 0);
        $header_system_id = trim( $request->header( 'SYSTEM-ID' ) ?: 0);
        $header_branch_id = trim( $request->header( 'BRANCH-ID' ) ?: 1);
        $header_source_url = trim( $request->header( 'SOURCE-URL' ));
        $header_user_account_id = trim( $request->header('PAY-USER-ACCOUNT-ID') ?: 0 );
        $pay_proxy_id = trim($request->header('PAY-PROXY-ID'));

        //FILTER
        $branches->where([
            "oauth_client_id"=>$header_client_id,
            "local_url"=>$header_source_url,
            "domain"=>$header_domain,
            //"local_branch_id"=>$header_branch_id,
            //"local_system_id"=>$header_system_id,
        ]);



        if($request->is_all){
            return $branches->get();
        }


        return $branches->paginate(10);
    }

    public function add_update(Request $request){

        //CHECK IF EXISTS
        $xrac_branch = null;

        $header_client_id = trim( $request->header('CLIENT-ID') ?: "" );
        //$header_client_secret = trim( $request->header('SECRET') ?: "");
        //$header_pay_url = trim( $request->header('PAY-URL') ?: "");
        $header_source_name = trim( $request->header( 'SOURCE-NAME' ));
        $header_domain = trim( $request->header( 'REQUESTOR-DOMAIN' ) ?: 0);
        $header_system_id = trim( $request->header( 'SYSTEM-ID' ) ?: 0);
        $header_branch_id = trim( $request->header( 'BRANCH-ID' ) ?: 1);
        $header_source_url = trim( $request->header( 'SOURCE-URL' ));
        $header_user_account_id = trim( $request->header('PAY-USER-ACCOUNT-ID') ?: 0 );
        $pay_proxy_id = trim($request->header('PAY-PROXY-ID'));

        $this->validate($request, [
            "branch_id"=>"required",
            "name"=>"required"
        ]);


        $xrac_branch = XracBranch::where([
            "domain"=>$header_domain,
            "oauth_client_id"=>$header_client_id,
            "local_branch_id"=>$request->branch_id,
            //"local_system_id"=>$header_system_id
        ])->withTrashed()->first();

        //UPDATE
        if($xrac_branch){
            $xrac_branch->local_system_id = $header_system_id;
            $xrac_branch->local_url = $header_source_url;
            $xrac_branch->is_active = $request->is_active || $request->is_active == 1 ? true : false;
            $xrac_branch->name = $request->name;
            $xrac_branch->deleted_at = $request->deleted_at;
            if($request->deleted_at){
                $xrac_branch->deleted_pay_user_account_id = $header_user_account_id;
            }else{
                $xrac_branch->updated_pay_user_account_id = $header_user_account_id;
            }
            $xrac_branch->save();

            return ["status"=>1, "message"=>"Update Details", "data"=>$xrac_branch];
        }

        //ADD
        $xrac_branch = XracBranch::create([
            "domain"=>$header_domain,
            "oauth_client_id"=>$header_client_id,
            "local_branch_id"=>$request->branch_id,
            "local_system_id"=>$header_system_id,
            "local_url"=>$header_source_url,
            "is_active"=>$request->is_active || $request->is_active == 1 ? true : false,
            "name"=>$request->name,
            "deleted_at"=>$request->deleted_at,
            "created_pay_user_account_id"=>$header_user_account_id
        ]);


        return ["status"=>1, "message"=>"Successfully Added", "data"=>$xrac_branch];



    }


}

<?php

namespace iProtek\Pay\Http\Controllers\Api;

use Illuminate\Http\Request;
use iProtek\Pay\Http\Controllers\_Common\_CommonController;
use iProtek\Pay\Models\AppLicense;
use iProtek\Pay\Models\AppUser;
use iProtek\Pay\Models\Application;
use iProtek\Pay\Models\LicenseInstalled;

class AppLicenseApiController extends _CommonController
{
    //
    public function index(Request $request){

        return $this->view('app-license');
    }

    //Getting Single only
    public function get_app_licenses(Request $request, $id = null){
        $licenses = AppLicense::with(["AppInfo","AppUserInfo","createdInfo","updatedInfo"])->whereRaw('bundle_id IS NULL');
        if($id){
            return $licenses->where('id', $id)->first();
        }

        $data_result =  $licenses->orderBy('id','DESC')->get();
        return ["data"=>$data_result];
    }

    //Getting bundle app
    public function get_bundle_app_licenses(Request $request){
        $data_result =  AppLicense::with(["AppInfo","AppUserInfo","createdInfo","updatedInfo"])->whereRaw('bundle_id IS NOT NULL')->orderBy('id','DESC')->get();
        return ["data"=>$data_result];
    }

    public function add_single_app_license(Request $request){
        $this->validate($request, [
            "license_no"=>"required|unique:app_licenses,license_no",
            "app_id"    =>"required",
            "cost"      =>"required",
            "duration"  =>"required",
            //"max_computer_install"  =>  "required"
        ]);

        $appLicense = AppLicense::create([
            "license_no"    =>  $request->license_no,
            "app_id"        =>  $request->app_id,
            "app_user_id"   =>  $request->app_user_id,
            "last_version"  =>  $request->app_version,
            "domain_name"   =>  $request->domain,
            "cost"          =>  $request->cost,
            "duration"      =>  $request->duration,
            "is_active"     =>  $request->is_active,
            "max_computer_install"  => $request->max_computer_install,
            "paid_info"     =>  json_encode($request->paid_info),
            "created_by"    => auth()->user()->id
        ]);



        return ["message"=>"Successfully Added", "app_license_id"=>$appLicense->id ];


    }

    public function update_single_app_license(Request $request, AppLicense $id ){

        $this->validate($request, [
            //"license_no"=>"required|unique:app_licenses,license_no",
            "app_id"    =>"required",
            "cost"      =>"required",
            "duration"  =>"required",
            //"max_computer_install"  =>  "required"
        ]);

        if(!$id->app_user_id){
            $id->update([
            //"app_id"        =>  $request->app_id,
            "app_user_id"   =>  $request->app_user_id,
            "last_version"  =>  $request->app_version,
            "domain_name"   =>  $request->domain,
            "cost"          =>  $request->cost,
            "duration"      =>  $request->duration,
            "is_active"     =>  $request->is_active,
            "max_computer_install"  => $request->max_computer_install,
            "paid_info"     =>  json_encode($request->paid_info),
            "updated_by"    => auth()->user()->id
            ]);
        }
        else{
            $id->update([
            //"app_id"        =>  $request->app_id,
            //"app_user_id"   =>  $request->app_user_id,
            "last_version"  =>  $request->app_version,
            "domain_name"   =>  $request->domain,
            "cost"          =>  $request->cost,
            "duration"      =>  $request->duration,
            "is_active"     =>  $request->is_active,
            "max_computer_install"  => $request->max_computer_install,
            "paid_info"     =>  json_encode($request->paid_info),
            "updated_by"    => auth()->user()->id
            ]);

        }

        return ["message"=>"Successfully Added", "app_license_id"=>$id->id ];
    }
    
    public function remove_single_app_license(AppLicense $id){
        $id->deleted_by =  auth()->user()->id;
        $id->timestamps = false;
        $id->save();
        $id->delete();
        return ["message"=>"Successfully Removed", "app_license_id"=>$id->id ];
    }

    public function validate_license(Request $request, Application $app_id){

        //Get application 
        if(!$app_id)
          return  response()->json(["message"=>"Application not found"], 403)->send();
        
        //Get the app user info
        $appUser = AppUser::where('account_no', $request->account_no)->first();
        if(!$appUser)
          return  response()->json(["message"=>"User not found"], 403)->send();
        
        //Get the app user license info

        $appLicense =  AppLicense::with('getInstalled')->where('app_user_id', $appUser->id)->where('app_id', $app_id->id)->where('license_no', $request->license_no)->first();
        if(!$appLicense)
            return  response()->json(["message"=>"User License not found"], 403)->send();

        //Application activation here.. for the activated_at and expire at.
        if(!$appLicense->activated_at){
            $appLicense->activated_at = \Carbon\Carbon::now();
            $appLicense->expired_at = \Carbon\Carbon::now()->addMonths($appLicense->duration);
            $appLicense->timestamps = false;
            $appLicense->save();
        }

        //Checking if its installed.
        $userInstalled = LicenseInstalled::where(['mac_id'=>$request->mac_id,'app_license_id'=>$appLicense->id])->first();
        if(!$userInstalled){
            //Checking if has installed already.
            if($appLicense->max_computer_install <= $appLicense->total_computer_installed){
                return  response()->json(["message"=>"License installed already at max limit."], 403)->send();
            }
            //ADD PC here.
            $userInstalled = LicenseInstalled::create([
                "app_license_id"=>$appLicense->id,
                "mac_id"=>$request->mac_id,
                "computer_name"=>$request->computer_name,
                "computer_ip"=>$request->computer_ip,
                "last_checked_at"=>\Carbon\Carbon::now()
            ]);
        }
        else{
            $userInstalled->last_checked_at = \Carbon\Carbon::now();
            $userInstalled->timestamps = false;
            $userInstalled->save();
        }

        //Update datetime checking        
        $appLicense->last_check_at = \Carbon\Carbon::now();
        $appLicense->timestamps = false;
        $appLicense->save();
        
        return ["app_user"=>$appUser, "license_info"=> $appLicense, "installed_info"=>$userInstalled];

        //return [ "license_no"=>$request->license_no, "account_no"=>$request->account_no, 'message'=>"Success"];
    }

}

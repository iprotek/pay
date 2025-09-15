<?php

namespace iProtek\Pay\Http\Controllers\Api;

use Illuminate\Http\Request;
use iProtek\Pay\Http\Controllers\_Common\_CommonController;
use iProtek\Pay\Models\Application;

class ApplicationApiController extends _CommonController
{
    //
    public $guard = 'admin';
    
    public function index(Request $request){

        return $this->view('application');
    }


    public function get_applications(Request $request, $id = null){
        $applications = Application::with([ 'stageInfo','deletedInfo', 'createdInfo', 'updatedInfo', 'requires',"rateInfos", "appModes"])->orderBy('id','DESC');
        if(auth('app_user')->check()){
            $applications->with('myLicenses');
        }
        
        if($id){
            return $applications->where('id', $id)->withTrashed()->first();
        }
        $data_result =  $applications->get();



        return ["data"=>$data_result];


    }

    public function get_application_code(Request $request, $app_code){
        return Application::with(['deletedInfo', 'createdInfo', 'updatedInfo'])
                            ->where('app_code', $app_code)
                            ->withTrashed()
                            ->first();
    }

    public function add_application(Request $request){
        
        
        $this->validate($request, [
            "app_code"=>"required|unique:applications,app_code",
            "app_name"=>"required|unique:applications,app_name",
            "app_type"=>"required",
            "current_version"=>"required",
            "cost"=>"required|between:0,999999.99",
            "initial_update_cost"=>"required|between:0,999999.99",
            "default_duration"=>"required|int"
        ]);

        //return ["message"=>"Successfully Added", "account_no"=>"" ];
        //Getting the last
        //return ["account_no"=>$new_account_no];
        $application = Application::create([
            "app_type"              => $request->app_type,
            "app_code"              => $request->app_code,
            "app_name"              => $request->app_name,
            "current_version"       => $request->current_version,
            "cost"                  => $request->cost,
            "initial_update_cost"   => $request->initial_update_cost,
            "default_duration"      => $request->default_duration,
            "created_by"            => auth()->user()->is_dir
        ]);


        return ["message"=>"Successfully Added", "app_id"=>$application->id ];

    }

    public function update_application(Request $request, Application $id){
        
        $this->validate($request, [
            "app_code"=>"required|unique:applications,app_code,".$id->id,
            "app_name"=>"required|unique:applications,app_name,".$id->id,
            "app_type"=>"required",
            "current_version"=>"required",
            "cost"=>"required|between:0,999999.99",
            "initial_update_cost"=>"required|between:0,999999.99",
            "default_duration"=>"required|int"
        ]);
        $id->app_code = $request->app_code;
        $id->app_name = $request->app_name;
        $id->app_type = $request->app_type;
        $id->current_version = $request->current_version;
        $id->cost = $request->cost;
        $id->initial_update_cost = $request->initial_update_cost;
        $id->default_duration = $request->default_duration;
        $id->updated_by = auth()->user()->id;
        $id->save();

        return  ["message"=>"Successfully Updated.", "app_id"=>$id->id ];


    }
    public function remove_application(Application $id){
        $id->deleted_by = auth()->user()->id;
        $id->timestamps = false;
        $id->save();
        $id->delete();
        return  ["message"=>"Successfully Removed.", "app_id"=>$id->id ];
    }


}

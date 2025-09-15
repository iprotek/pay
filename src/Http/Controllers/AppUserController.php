<?php

namespace iProtek\Pay\Http\Controllers;

use Illuminate\Http\Request;
use iProtek\Pay\Http\Controllers\_Common\_CommonController;
use iProtek\Pay\Models\AppUser;
use DB;
use Illuminate\Validation\Rule;

class AppUserController extends _CommonController
{
    //
    public $guard = 'admin';
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request){

        return $this->view('app-user');
    }

    public function get_app_users(Request $request, $id = null){

        $app_users = AppUser::with('registerByInfo');
        if($id){
           return $app_users->where('id', $id)->first();
        }

        $data_result = $app_users->orderBy('id','DESC')->get();

        return ["data"=>$data_result];
    }

    public function add_app_user(Request $request){
        
        $this->validate($request, [
            "first_name"=>"required",
            "last_name"=>"required",
            "display_name"=>"required",
            "contact_no"=>"required|unique:app_users,contact_no",
            "email"=>"required|unique:app_users,email"
        ]);

        //return ["message"=>"Successfully Added", "account_no"=>"" ];
        //Getting the last
        $new_account_no = DB::select(" SELECT  ( IF( IFNULL(max(account_no * 1), 10000) < 10000, 10000, max(account_no * 1)) +1) as new_account_no FROM app_users WHERE account_no IS NOT NULL " )[0]->new_account_no;
        //return ["account_no"=>$new_account_no];
        $app_user = AppUser::create([
            "account_no"    => $new_account_no,
            "first_name"    => $request->first_name,
            "last_name"     => $request->last_name,
            "display_name"  => $request->display_name,
            "contact_no"    => $request->contact_no,
            "email"         => $request->email,
            "address"       => $request->address,
            "company"       => $request->company,
            "created_by"    => auth()->user()->id
        ]);


        return ["message"=>"Successfully Added", "account_no"=>$app_user->account_no ];
    }

    public function update_app_user(Request $request, AppUser $id){
        
        $this->validate($request, [
            "first_name"=>"required",
            "last_name"=>"required",
            "display_name"=>"required",
            "contact_no"=>"required|unique:app_users,contact_no,".$id->id,
            "email"=>"required|unique:app_users,email,".$id->id
        ]);

        $id->first_name = $request->first_name;
        $id->last_name = $request->last_name;        
        $id->display_name = $request->display_name;        
        $id->contact_no = $request->contact_no;        
        $id->email = $request->email;
        $id->address = $request->address;
        $id->company = $request->company;
        $id->updated_by = auth()->user()->id;
        $id->save();
        



        return ["message"=>"Successfully Updated", "account_no"=>$id->account_no];
    }

    
    public function remove_app_user(AppUser $id){

        $id->deleted_by = auth()->user()->id;
        $id->timestamps = false;
        $id->save();
        $id->delete();
        return ["message"=>"Successfully Removed", "account_no"=>$id->account_no];

    }
}

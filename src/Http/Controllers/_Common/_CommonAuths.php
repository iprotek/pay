<?php

namespace iProtek\Pay\Http\Controllers\_Common;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\_Common\_CommonController;
use App\Models\Position;
use App\Models\Department;
use App\Models\Factory;
use App\Models\Region;
use App\Models\UserAdmin;
use App\Helpers\LanguageHelper;

class _CommonAuths extends _CommonController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public const AuthoLoginAfterRegister = true;


    public function __construct()
    {
        $this->middleware(['guest', 'throttle:5,1'] )->except('logout');
    }
    
    //email, password, noreload, remember
    public function login(Request $request)
    {

       $request->validate([
            'email' => ['required']
        ]);
        if(LanguageHelper::get_mode() == 'edit')
            return $this->_CResult(0, "Edit Mode", 0);

        $noreload = $request['noreload'];
        $password = $request['password'];

        if(empty($password))
        {
            if (Auth::guard($this->guard)->attempt(['username' => $request['email'], 'password'=>'password', 'nopass' => 1], $request['remember'] )) {
                if($noreload == 1 )
                    return $this->_CResult(1, "Succeed", 0);
            }
        }
        else if (Auth::guard($this->guard)->attempt(['email' => $request['email'], 'password' => $request['password']], $request['remember'] )) {
            if($noreload == 1 )
                return $this->_CResult(1, "Succeed", 0);
            return redirect('/'.$this->guard);
        }
        else if (Auth::guard($this->guard)->attempt(['username' => $request['email'], 'password' => $request['password']], $request['remember'] )) {
            if($noreload == 1 )
                return $this->_CResult(1, "Succeed", 0);
            return redirect('/'.$this->guard);
        }
        return $this->_CResult(0, "Failed", 0);
    }
    public function register(Request $request)
    {

       $request->validate([
            'username'=> ['required'],
            //'email' => ['required'],
            'first_name'=>['required'],
            'last_name'=>['required'],
            'factory_id'=>['required'],
            'department_id'=>['required'],
            'line'=>['required'],
            'position_id'=>['required'],
            'status_id'=>['required']
        ]);

        $first_name = $request['first_name'];
        $last_name = $request['last_name'];
        $middle_name = $request['middle_name'] ?? "";
        $username = $request['username'];

        $fullname = $first_name.' '.$last_name;
        $email_guess = $first_name.'.'.$last_name."-guest.guest.com.ph";

        /**CHECK If email exists */
        $user =  DB::table('user_'.$this->guard.'s')->whereRaw(' company_id = ? OR username = ? OR email = ?', [$username, $username, $email_guess])->get();
        if(count($user) > 0)
            return $this->_CResult(0, "User already exists", -1);


        //ID SELECTIONS
        $position_id = $request['position_id'];
        $factory_id = $request['factory_id'];
        $department_id = $request['department_id'];
        $line = $request['line'];
        $status_id = $request['status_id'];
        $email = $request['email'];

        
        $position_name = '';
        $position = Position::find($position_id);
        if($position == null){
            return $this->_CResult(0, "Invalid(1)", -1);
        }
        $position_name = $position->name;


        $department_name = '';
        $department = Department::find($department_id);
        if($department == null){
            return $this->_CResult(0, "Invalid(2)", -1);
        }
        $department_name = $department->name;

        $factory_name = '';
        $region_name = '';
        $factory = Factory::find($factory_id);
        if($factory == null){
            return $this->_CResult(0, "Invalid(3)", -1);
        }
        $factory_name = $factory->name;
        $region = Region::find($factory->region_id);
        if($region == null){
            return $this->_CResult(0, "Invalid(4)".$factory->name."REGION ID", -1);
        }
        $region_name = $region->name;



        $id = UserAdmin::create([
            'name' => $fullname,
            'username'=>$username,
            'company_id'=>$username,
            'email' => $email ?? $email_guess,
            'is_verified' => 1,
            'user_type'=>1,
            'nopass'=>1,
            'password' => bcrypt('password'),
            'region' => $region_name
        ])->id;

        DB::table('user_admin_infos')->insert([
            'user_admin_id'=>$id,
            'company_id'=>$username,
            'first_name'=>$first_name,
            'middle_name'=>$middle_name,
            'last_name'=>$last_name,
            'position'=>$position_name,
            'department'=>$department_name,
            'line'=>$line,
            'factory'=>$factory_name,
            'is_active'=>1,
            'status_id'=>$status_id,
            'region'=>$region_name
        ]);

        if(static::AuthoLoginAfterRegister){
            
            if ( Auth::guard($this->guard)->attempt(['username' => $username, 'password'=>'password', 'nopass' => 1], null )) {
                    return $this->_CResult(1, "Succeed", 1);
            }
            
        }


        return $this->_CResult(1, "Successfully Added", 0);
    }

    public function logout()
    {
        //if(Auth::guard($this->guard)->user()!= null)
        Auth::guard($this->guard)->logout();
        return redirect('/'.$this->guard.'/login');
    }
    /*
    public function loginpage()
    {
        if(Auth::guard($this->guard)->user() != null)
            return redirect('/'.$this->guard);
        else
            return view($this->guard.'.login');
    }
    */
    public function registerpage()
    {
        if(Auth::guard($this->guard)->user() != null)
            return redirect('/'.$this->guard);
        else
            return view($this->guard.'.register');
    }
}


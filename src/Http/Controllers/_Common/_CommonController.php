<?php

namespace iProtek\Pay\Http\Controllers\_Common;

use Illuminate\Routing\Controller as BaseController;

use App\Helpers\_TemplateHelper as Template;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Helpers\LanguageHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Session;

class _CommonController extends BaseController
{
    public function view($view, $array = null){
        if(!$array){
            $array = $this->common_infos();
        }
        return view($view, $array);
    }

    public function validate(Request $request, $args = array(), $custom=array()){
        
        $validator = Validator::make($request->all(), 
            $args,
            $custom
        );
        if ($validator->fails()) {    
             response()->json($validator->messages(), 403)->send();
            die();
        }
    }
    
    public function validator(Request $request, array $rules, $is_session = true){

        
        $validator = Validator::make($request->all(), $rules);
        $errors = [  ];              

        if($validator->fails()){
            $temp_error = $validator->errors();
            $arr_keys = $temp_error->keys();
           
            /*
            var_dump($temp_error);
            var_dump($arr_key);
            die();
            */
            foreach($arr_keys as $key){
                $errors[$key] = implode(',', $temp_error->get($key) ); //implode(',', $temp_error->password);
            }
            //var_dump($errors);
            //die();
        }


        if($is_session){
            Session::put('errors', $errors);
            if(count($errors)> 0)
                return false;
            return true;
        }
        
        return $validator;
    }

    public function _CResult($retval, $message, $dataid)
    {
        return [ "RetVal"=>$retval, "Message"=>$message, "DataID"=>$dataid  ];
    }
    public function _SResult($retval, $data, $itemperpage, $page, $itemsfound, $returnValue = 1)
    {
        return [ "RetVal"=>$retval, "Data"=>$data, "ItemPerPage"=>$itemperpage,"Page"=>$page,"ItemsFound"=> $itemsfound, "ReturnValue"=>$returnValue ];
    }
    public function common_infos()
    {
        $user = Auth::guard($this->guard)->user();
        $sidemenus = json_decode( DB::select("SELECT fnSysGetGroupSidemenus(?,0) as sidemenus ",[$user->id])[0]->sidemenus) ;//->sidemenus;
        
        $user_type = $user->user_type;

        if($user->user_type !== null ){
            
            $new_array = [];
            foreach($sidemenus  as $sideItem){
                
                if($sideItem->user_types != null && $sideItem->user_types != ""){
                   if( in_array( $user_type, explode(",",$sideItem->user_types) ) ){
                        $new_array[] = $sideItem;

                        $newList = [];
                        //Filter inside the items
                        foreach($sideItem->items as $subItem){
                            
                            //if($subItem->user_types != null && $subItem->user_types != ""){ 
                                //var_dump($subItem->user_types);
                                if(  in_array( $user_type, explode(",", $subItem->user_types) ) ){
                                //if($subItem->is_allowed == "1"){
                                    $newList[] = $subItem;
                               // }
                                }
                           // else
                            //    $newList[] = $subItem;
                        }
                        $sideItem->items = $newList;

                   }
                }
                else
                    $new_array[] = $sideItem;
            }
            $sidemenus = $new_array;
        }
        //var_dump($sidemenus[0]->items);
       // abort(200);

        return ["SIDEMENUS"=>$sidemenus, "USER"=>$user];
    }

    public function loginpage()
    {
        if(Auth::guard($this->guard)->user() != null && LanguageHelper::get_mode() != 'edit' )
            return redirect('/'.$this->guard);
        else
            return view($this->guard.'.login');
    }
    
 
    public function accounts_page()
    {
        $infos = $this->common_infos();
        return view($this->guard.'.accounts',$infos);
    }
    
    public function categories_page()
    {
        $infos = $this->common_infos();
        return view($this->guard.'.categories', $infos);
    }

    public function factories_page()
    {
        $infos = $this->common_infos();
        return view($this->guard.'.factories', $infos);
    }

    public function regions_page()
    {
        $infos = $this->common_infos();
        return view($this->guard.'.regions', $infos);
    }

    public function devices_page()
    {
        $infos = $this->common_infos();
        return view($this->guard.'.devices', $infos);
    }
    
    public function for_approval_page()
    {
        if(auth()->user()->can_approve != "1")        
            abort(404);
        $infos = $this->common_infos();
        return view($this->guard.'.for-approval', $infos);
    }
    public function for_classification_page()
    {
        if(auth()->user()->can_classify != "1")        
            abort(404);
        $infos = $this->common_infos();
        return view($this->guard.'.for-classification', $infos);
    }
    public function for_evaluation_page()
    {
        if(auth()->user()->can_evaluate != "1")        
            abort(404);
        $infos = $this->common_infos();
        return view($this->guard.'.for-evaluation', $infos);
    }
    public function for_implementation_page()
    {
        if(auth()->user()->can_implement != "1")        
            abort(404);
        $infos = $this->common_infos();
        return view($this->guard.'.for-implementation', $infos);
    }
    public function suggestion_list_page()
    {
        $infos = $this->common_infos();
        $created_by = auth('admin')->user()->id;
        $count = Suggestion::select('id')->where('created_by', $created_by)->get()->count();
        $infos['suggestion_count'] = $count;
        return view($this->guard.'.suggestion-list', $infos);
    }
}

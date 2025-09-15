<?php

namespace iProtek\Pay\Http\Controllers\Manage;

use Illuminate\Http\Request;
use iProtek\Pay\Models\AppUserAccount;
use Laravel\Passport\Client;
use iProtek\Pay\Http\Controllers\_Common\_CommonController;
use iProtek\Pay\Helpers\AppUserAccountHelper;
use \DB;

class AppUserAccountController extends _CommonController
{
    //
    public $guard = 'admin';
    public function index(Request $request){        
        return $this->view('manage.customer.index');
    }

    public function list(Request $request){
        $user_id = auth()->user()->id;
        $client_id = $request->client_id; 
        $clients_req = Client::where('user_id', $user_id); 
        if($client_id > 0){
            $clients_req->where('id', $client_id);
        }

        $clients = $clients_req->get(); 

        $client_ids = [];
        foreach($clients as $client){
            $client_ids[] = $client->id;
        }
        
        
        $user_accounts = AppUserAccount::with(['oauth_client'=>function($q){
            $q->select('id', 'name');
        }]); 
        $user_accounts->whereIn('oauth_client_id', $client_ids);


        return $user_accounts->paginate(10);
        //$passport_clients->orderBy('id','DESC');

        //return null;
        //return $passport_clients->paginate(10);
    }

    public function list_client_selection(Request $request){
        //User Admin
        $user_id = auth()->user()->id;
        $clients = Client::where('user_id', $user_id);
        if($request->search_text){
            $search_text = str_replace(' ', '%', $request->search_text);
            $clients->whereRaw('name like ?',['%'.$search_text.'%']);
        }
        $clients->select('name as text', 'id');
        //$clients = \DB::table('oauth_clients')->where('user_id', $user_id);
        return $clients->paginate(5);
    }

    public function get_client_app_user_account(Request $request, Client $client_id, AppUserAccount $id){
        $user_id = auth()->user()->id;
        $client_id = $request->client_id;

        //Check ownership
        if($client_id->user_id != $user_id){
            return response()->json(['error' => "Unauthorized"], 401);
        }
        
        if($id->oauth_client_id != $client_id->id){
            return response()->json(['error'=>'Unauthorized2'], 401);
        }
        return $id;

    }

    public function add_app_user_account(Request $request, Client $client_id){
        $this->validate($request, [ 
            "email"=>"required|email",
            "name"=>"required|min:3"
        ]);

        $admin_user_id = auth()->user()->id;
        if($client_id->user_id != $admin_user_id){
            return response()->json(['error'=>'Unauthorized'], 401);
        }

        //Check ownership of the client_id
        return AppUserAccountHelper::add_app_user_account($request, $client_id->id, $admin_user_id, 0);
    }

}

<?php

namespace iProtek\Pay\Models;
 
use Laravel\Passport\Client;

class ClientInfo extends Client
{ 


    public function socket_info(){
        return $this->hasOne(MessageSocket::class, 'oauth_client_id');
    } 

    public function apps(){
        return $this->hasMany(XracDomain::class, 'oauth_client_id')->select('id', 'oauth_client_id', 'local_url', 'local_system_id', 'name' );
        $domains = XracDomain::where('oauth_client_id', $this->id);
        $domains->select('id', 'oauth_client_id', 'local_url', 'local_system_id', 'name' );
        return $domains->get();
    }
}

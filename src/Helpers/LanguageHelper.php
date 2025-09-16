<?php

namespace iProtek\Pay\Helpers;
use Illuminate\Support\Facades\Session;
use App\Models\LanguageList;
use DB;

class LanguageHelper
{

    public static function get_translation($label_trans_id, $defaultWord, $lang=null){

        if($lang == null)
            $lang = static::get_language();

        $Words = DB::connection('mysql_translation')->select(" select get_translation(?, ?) as words ",[$label_trans_id, $lang ])[0]->words;
        if(empty( trim( $Words )))
            return $defaultWord;
        return $Words;
    }

    public static function get_language(){
        //$suggestions = static::suggestion_list($request);

        //return $suggestions;
       $language = Session::get('translation-language');
       //if(auth('admin')->user()->)
       $Lang = LanguageList::where('code', $language)->first();
       //return $Lang;
       if($Lang == null){
            $user = auth('admin')->user();
            if($user != null){
                $language = $user->lang;
                $Lang = LanguageList::where('code', $language)->first();
                if($Lang == null){
                    $Lang = LanguageList::first();
                    if($Lang == null)
                        return 'en';
                }
            }
            else{
                $Lang = LanguageList::first();
                if($Lang == null)
                    return 'en';
            }

       }
       return $Lang->code;
    }

    public static function set_language($language = ''){

        $Lang = LanguageList::where('code', $language)->first();
        $language = 'en';
        if($Lang == null){
            $Lang = LanguageList::first();
            if($Lang == null)
            {
                Session::put('translation-language', $language);
                return $language;
            }
        }
        $language = $Lang->code ?: 'en';
        Session::put('translation-language', $language );
        return $language;

    }

    public static function mode(){
        $language = Session::get('translation-language');
    }
    public static function set_mode($mode){
        //MODES
        //edit
        //none
        $user = auth('admin')->user();
        if($user == null)
            $mode = 'none';        
        $mode = Session::put('mode_'.$user->id, $mode);
        return $mode;
    }
    public static function  get_mode(){
        $user = auth('admin')->user();
        if($user == null)
            return 'none';        
        $mode = Session::get('mode_'.$user->id);
        return $mode ?: 'none';
    }


}

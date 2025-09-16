<?php

namespace iProtek\Pay\Helpers;

use iProtek\Pay\Helpers\AdminLTEHelper as DesignHelper;
use iProtek\Pay\Helpers\LanguageHelper;

class AdminLTEHelper
{

    public static function _getLabelTranslateID($attributes){
        
        $label_trans_id = $attributes['x-label-trans-id'];
        return $label_trans_id ?: '';
    }

    //BUTTON DESIGNS
    public static function _Button1($text, $attributes )
    {
        return '<button '.$attributes.'>'.$text.'</button>';
    }
    public static function _ButtonApp($icon, $text, $attributes)
    {
       return'<button '.$attributes.'>
                  <i class="fas '.$icon.'"></i>'.$text.'
                </button>';
    }
    public static function _ButtonSocial($icon, $text, $attributes)
    {
        //class="btn btn-social btn-primary"
        //data-source="LoginInfo" sys-submit="LoginSend" sys-submit-method="POST" sys-submit-url="v2/Data/AdminLogin" sys-send-text="Login.. Please wait" sys-result-text="Login Successful"
        
        $label_trans_id = static::_getLabelTranslateID($attributes);
        $label_trans = '';
        if(!empty($label_trans_id)){
            $label_trans = 'label-trans-id="'.$label_trans_id.'"';
        }   
        $is_edit = LanguageHelper::get_mode() == 'edit';
        $pointer_event = $is_edit ? '' : 'pointer-events: none;';
        
        return '<button '.$attributes.'>
                <i class="fa '.$icon.'" style="pointer-events: none;"></i> <label style="'.$pointer_event.'margin-bottom:0px;" '.$label_trans.'> '.(LanguageHelper::get_translation($label_trans_id, $text) ).' </label>
            </button>';
    }


    public static function _InputText1($icon, $placeholder,  $attributes)
    {
        return '<div class="input-group mb-3">
        <input placeholder="'.$placeholder.'" '.$attributes.'>
        <div class="input-group-append">
            <div class="input-group-text">
            <span class="fas '.$icon.'"></span>
            </div>
        </div>
        </div>';
    }



    public static function _CheckBox1($id, $text, $attributes)
    {
        //{!! App\Helpers\LanguageHelper::get_translation(  $label_trans_id, $text)  !!}
        $label_trans_id = static::_getLabelTranslateID($attributes);
        $label_trans = '';
        if(!empty($label_trans_id)){
            $label_trans = 'label-trans-id="'.$label_trans_id.'"';
        }   
        return '
        <div class="icheck-primary">
        <input id="'.$id.'" type="checkbox" '.$attributes.' >
            <label for="'.$id.'" '.$label_trans.'>
                '.( LanguageHelper::get_translation($label_trans_id, $text ) ).'
            </label>
        </div>';
    }
    public static function _SideBarMenuItem($id, $text, $url, $attributes)
    {
        
        return 
        '<li class="nav-item">
            <a href="'.$url.'" id="side-bar-menu-item-'.$id.'" class="nav-link active">
            <i class="nav-icon fas fa-th"></i>
            <p>
                '.$text.'
                <!-- <span class="right badge badge-danger">New</span> -->
            </p>
            </a>
        </li>';
    }



    public static function _ProgressBar($text, $value, $attributes)
    {
        //class="progress-bar bg-primary progress-bar-striped"
        //class="sr-only" span
        return '<div class="progress">
                    <div '.$attributes.' role="progressbar" aria-valuenow="'.$value.'" aria-valuemin="0" aria-valuemax="100" style="width: '.$value.'%">
                    <span class="progress-description"> <span class="progress-value">'.$value.'</span>% '.$text.'</span>
                    </div>
                </div>';
    }

    public static function _Container($content)
    {
        return '<label>Hello</label>'.$content;
    }
}

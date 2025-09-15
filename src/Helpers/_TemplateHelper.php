<?php

namespace iProtek\Pay\Helpers;

use App\Helpers\AdminLTEHelper as DesignHelper;
use App\Helpers\_TemplateFunctionHelper;

class _TemplateHelper extends _TemplateFunctionHelper
{
    /**
     * BUTTONS
     */

    public static function _Button($text, $attributes )
    {
        //$attributes = "";
        if($attributes === [] || $attributes === null)
        {
            $attributes = ["type"=>"submit"];
        }
        $attributes = self::_CombineClass($attributes, "btn btn-block");
        $attributes = self::_Attributes($attributes);
        return DesignHelper::_Button1($text, $attributes);

    }
    public static function _ButtonSocial($text, $attributes=null )
    {
        //$attributes = "";
        
        if($attributes === [] || $attributes === null)
        {
            $attributes = ["type"=>"submit"];
        }
        $attributes = self::_CombineClass($attributes, "btn");
        $icon = $attributes['x-icon'];
        $attributes = self::_Attributes($attributes);
        return DesignHelper::_ButtonSocial($icon, $text, $attributes);

    }
    public static function _Button1($text, $attributes = null)
    {
        $attributes = self::_CombineClass($attributes, "btn-default btn-block");
       return self::_Button($text, $attributes);
    }
    //button1
    public static function _Button1Primary($text, $attributes = null)
    {
        $attributes = self::_CombineClass($attributes, "btn-primary btn-block");
        return self::_Button($text, $attributes);
    }
    public static function _Button1Secondary($text, $attributes = null)
    {
        $attributes = self::_CombineClass($attributes, "btn-secondary btn-block");
        return self::_Button($text, $attributes);
    }
    public static function _Button1Danger($text, $attributes=null)
    {
        $attributes = self::_CombineClass($attributes, "btn-danger btn-block");
        return self::_Button($text, $attributes);
    }
    public static function _Button1Warning($text, $attributes = null)
    {
        $attributes = self::_CombineClass($attributes, "btn-warning btn-block");
        return self::_Button($text, $attributes);
    }
    public static function _Button1Success($text, $attributes = null)
    {
        $attributes = self::_CombineClass($attributes, "btn-success btn-block");
        return self::_Button($text, $attributes);
    }
    //button2
    public static function _Button2($text, $attributes = null)
    {
        $attributes = self::_CombineClass($attributes, "btn-block btn-outline-default");
        return self::_Button($text, $attributes);
    }
    public static function _Button2Primary($text, $attributes = null)
    {
        $attributes = self::_CombineClass($attributes, "btn-block btn-outline-primary");
        return self::_Button($text, $attributes);
    }
    public static function _Button2Secondary($text, $attributes = null)
    {
        $attributes = self::_CombineClass($attributes, "btn-block btn-outline-secondary");
        return self::_Button($text, $attributes);
    }
    public static function _Button2Danger($text, $attributes=null)
    {
        $attributes = self::_CombineClass($attributes, "btn-block btn-outline-danger");
        return self::_Button($text, $attributes);
    }
    public static function _Button2Warning($text, $attributes = null)
    {
        $attributes = self::_CombineClass($attributes, "btn-block btn-outline-warning");
        return self::_Button($text, $attributes);
    }
    public static function _Button2Success($text, $attributes = null)
    {
        $attributes = self::_CombineClass($attributes, "btn-block btn-outline-success");
        return self::_Button($text, $attributes);
    }
    //button3
    public static function _Button3($text, $attributes = null)
    {
        $attributes = self::_CombineClass($attributes, "btn-block bg-gradient-default");
        return self::_Button($text, $attributes);
    }
    public static function _Button3Primary($text, $attributes = null)
    {
        $attributes = self::_CombineClass($attributes, "btn-block bg-gradient-primary");
        return self::_Button($text, $attributes);
    }
    public static function _Button3Secondary($text, $attributes = null)
    {
        $attributes = self::_CombineClass($attributes, "btn-block bg-gradient-secondary");
        return self::_Button($text, $attributes);
    }
    public static function _Button3Danger($text, $attributes=null)
    {
        $attributes = self::_CombineClass($attributes, "btn-block bg-gradient-danger");
        return self::_Button($text, $attributes);
    }
    public static function _Button3Warning($text, $attributes = null)
    {
        $attributes = self::_CombineClass($attributes, "btn-block bg-gradient-warning");
        return self::_Button($text, $attributes);
    }
    public static function _Button3Success($text, $attributes = null)
    {
        $attributes = self::_CombineClass($attributes, "btn-block bg-gradient-success");
        return self::_Button($text, $attributes);
    }
    //button4
    public static function _Button4($text, $attributes = null)
    {
        $attributes = self::_CombineClass($attributes, "btn-social btn-default");
        return self::_ButtonSocial($text, $attributes);
    }
    public static function _Button4Primary($text, $attributes = null)
    {
        $attributes = self::_CombineClass($attributes, "btn-social btn-primary");
        return self::_ButtonSocial($text, $attributes);
    }
    public static function _Button4Secondary($text, $attributes = null)
    {
        $attributes = self::_CombineClass($attributes, "btn-social btn-secondary");
        return self::_ButtonSocial($text, $attributes);
    }
    public static function _Button4Danger($text, $attributes=null)
    {
        $attributes = self::_CombineClass($attributes, "btn-social btn-danger");
        return self::_ButtonSocial($text, $attributes);
    }
    public static function _Button4Warning($text, $attributes = null)
    {
        $attributes = self::_CombineClass($attributes, "btn-social btn-warning");
        return self::_ButtonSocial($text, $attributes);
    }
    public static function _Button4Success($text, $attributes = null)
    {
        $attributes = self::_CombineClass($attributes, "btn-social btn-success");
        return self::_ButtonSocial($text, $attributes);
    }
    //icon
    public static function _ButtonApp($icon, $text, $attributes = null)
    {
        //btn btn-app
        $attributes = self::_CombineClass($attributes, "btn btn-app");
        return DesignHelper::_ButtonApp($icon, $text, $attributes);
    }

    //progressbar
    public static function _ProgressBar($text, $value = 0, $attributes = null)
    {
        $kind = "";
        if($attributes['x-kind'] == "progressbar2")
            $kind = " progress-bar-striped";

        $type = " bg-primary";
        if($attributes['x-type'] != null || $attributes['x-type'] != "")
            $type = " bg-".$attributes['x-type'];

        $attributes = self::_CombineClass($attributes, "progress-bar".$kind.$type);
        $attributes = self::_Attributes($attributes);
        return DesignHelper::_ProgressBar( $text, $value, $attributes);
    }


    /**
     * TEXTBOXES 
     * */
    public static function _InputText1($icon, $type, $placeholder,  $attributes = null)
    {
        $attributes = self::_CombineClass($attributes, "form-control");
        $attributes['type'] = $type;
        $attributes = self::_Attributes($attributes);
        return DesignHelper::_InputText1($icon, $placeholder,  $attributes);
    }

    /**
     * CHECKBOXES
     * */
    public static function _CheckBox1($id, $text, $attributes = null)
    {
        $attributes = self::_CombineClass($attributes, "form-control");
        $attributes = self::_Attributes($attributes);
        return DesignHelper::_CheckBox1($id, $text, $attributes);
    }

}

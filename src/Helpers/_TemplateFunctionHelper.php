<?php

namespace iProtek\Pay\Helpers;


class _TemplateFunctionHelper
{
    //attributes to string
    public static function _Attributes($attributes = [])
    {
        $returnValue = $attributes;
        if(is_array($attributes))
        {
            $returnValue = "";
            foreach($attributes as $key => $val) {
                $returnValue = $returnValue.' '.$key.'="'.$val.'"';
            }
        }
        return $returnValue;
    }
    public static function _CombineClass($attributes, $class)
    {
        if($attributes == NULL)
        {
            $attributes = [];
            $attributes["class"] = $class;
        }
        else if($attributes === [])
            $attributes["class"] = $class;
        else if(is_array($attributes))
            $attributes["class"] = ($attributes["class"] ?? " ").$class;
        else
            $attributes = $attributes->merge(['class' => $class]);
            //$attributes = $attributes.' class= "'.$class.'"';
        return  $attributes;
    }
}
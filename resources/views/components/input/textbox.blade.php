<?php
    use App\Helpers\_TemplateHelper as Template; 
    
    $icon = $attributes['x-icon'];
    $type = $attributes['x-type'];
    $text = $attributes['x-text'];
    echo Template::_InputText1($icon, $type, $text, $attributes);
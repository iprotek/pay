<?php
    use App\Helpers\_TemplateHelper as Template; 
    
    $type = $attributes['x-type']; // primary, secondary, danger, warning, success
    $kind = $attributes['x-kind']; // button1, button2, button3
    $text = $attributes['x-text']; // Display text for button
    $icon = $attributes['x-icon']; // Display icon for button

    if($kind == 'icon')
    {
        echo Template::_ButtonApp($icon, $text, $attributes);
    }
    else if($type == 'primary')
    {
        if($kind == 'button2')
            echo Template::_Button2Primary($text ?? "Button", $attributes);
        else if($kind == 'button3')
            echo Template::_Button3Primary($text ?? "Button", $attributes);
        else if($kind == 'button4')
            echo Template::_Button4Primary($text ?? "Button", $attributes);
        else
            echo Template::_Button1Primary($text ?? "Button", $attributes);
    }
    else if($type =='secondary')
    {
        if($kind == 'button2')
            echo Template::_Button2Secondary($text ?? "Button", $attributes);
        else if($kind == 'button3')
            echo Template::_Button3Secondary($text ?? "Button", $attributes);
        else if($kind == 'button4')
            echo Template::_Button4Secondary($text ?? "Button", $attributes);
        else
            echo Template::_Button1Secondary($text ?? "Button", $attributes);
    }
    else if($type =='warning')
    {
        if($kind == 'button2')
            echo Template::_Button2Warning($text ?? "Button", $attributes);
        else if($kind == 'button3')
            echo Template::_Button3Warning($text ?? "Button", $attributes);
        else if($kind == 'button4')
            echo Template::_Button4Warning($text ?? "Button", $attributes);
        else
            echo Template::_Button1Warning($text ?? "Button", $attributes);
    }
    else if($type =='danger')
    {
        if($kind == 'button2')
            echo Template::_Button2Danger($text ?? "Button", $attributes);
        else if($kind == 'button3')
            echo Template::_Button3Danger($text ?? "Button", $attributes);
        else if($kind == 'button4')
            echo Template::_Button4Danger($text ?? "Button", $attributes);
        else
            echo Template::_Button1Danger($text ?? "Button", $attributes);
    }
    else if($type =='success')
    {
        if($kind == 'button2')
            echo Template::_Button2Success($text ?? "Button", $attributes);
        else if($kind == 'button3')
            echo Template::_Button3Success($text ?? "Button", $attributes);
        else if($kind == 'button4')
            echo Template::_Button4Success($text ?? "Button", $attributes);
        else
            echo Template::_Button1Success($text ?? "Button", $attributes);
    }
    else
    {
        if($kind == 'button2')
            echo Template::_Button2($text ?? "Button", $attributes);
        else if($kind == 'button3')
            echo Template::_Button3($text ?? "Button", $attributes);
        else if($kind == 'button4')
            echo Template::_Button4($text ?? "Button", $attributes);
        else
            echo Template::_Button1($text ?? "Button", $attributes);
    }
?>

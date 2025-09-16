<?php
    use App\Helpers\_TemplateHelper as Template; 

    $text = $attributes['x-text'];
    $id = $attributes['x-id'];
    echo Template::_CheckBox1($id, $text, $attributes);
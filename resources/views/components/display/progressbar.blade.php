<?php
    use App\Helpers\_TemplateHelper as Template; 
    $text = $attributes['x-text']; //
    $value = $attributes['x-value']; // 1 - 100
    //$attributes['x-type']; //primary, success, warning, danger
    // x-kind => (default)progressbar1 , progressbar2

    echo Template::_ProgressBar($text, $value, $attributes);

?>
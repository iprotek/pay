<?php
   // use Illuminate\Support\Facades\View;

    //if($attributes['x-title-from-section'] !== NULL || $attributes['x-title-from-section'] !== "" )
      //  $title = View::getSection($attributes['x-title-from-section']);
   // else
    $title = $attributes['x-title'];
    $type = $attributes['x-type'];
    $submitAttributes = $attributes['submit-attributes'];
?>

<div class="card card-<?=$type?>">
    <div class="card-header">
        <h3 class="card-title"><?=$title?></h3>
    </div>
    <div class="card-body">
        <?=$slot?>
    </div>
    <div class="card-footer">
        <input type="submit" <?=$submitAttributes?> />
    </div>
</div>
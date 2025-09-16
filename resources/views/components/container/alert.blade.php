<?php
    $title = $attributes['x-title'];
    $icon = $attributes['x-icon'];
    $type = $attributes['x-type']; //alert-danger, alert-info, alert-warning, alert-success


?>
<div class="alert <?=$type?> alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    <h5><i class="icon fas <?=$icon?>"></i><?=$title?></h5>
    <!--Danger alert preview. This alert is dismissable. A wonderful serenity has taken possession of my
    entire
    soul, like these sweet mornings of spring which I enjoy with my whole heart.
    -->
    <?=$slot?>
</div>
<?php
    $type = $attributes['x-type']; //callout-danger, callout-info, callout-warning, callout-success
    $title = $attributes['x-title'];
?>

<div class="callout <?=$type?>">
    <h5><?=$title?></h5>
    <p><!--There is a problem that we need to fix. A wonderful serenity has taken possession of my entire
    soul,
    like these sweet mornings of spring which I enjoy with my whole heart.-->
        <?=$slot?>
    </p>
</div>
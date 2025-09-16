<?php
    $text = $attributes['x-text'];
    $icon = $attributes['x-icon'];
    $badges = strval($attributes['badges'] ?? '[]');//json_decode( strval($attributes['badges'] ?? '[]') );  // done in v-bind:badges
    $badges = json_decode(html_entity_decode($badges)); //convert html entities into appropriate characters
?>

<li class="nav-item">
    <a href="#" class="nav-link">
        <i class="nav-icon fas <?=$icon?>"></i>
        <p>
        <?=$text?>
        <i class="right fas fa-angle-left"></i>
        <!-- <span class="badge badge-info right">6</span> -->

        <?php for($i = 0; $i < count($badges); $i++){
                $badge = $badges[$i];
            ?>
                <span class="right badge <?=$badge->class?>"> <?=$badge->text?> </span>
            <?php } ?>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <?=$slot?>
    </ul>
</li>
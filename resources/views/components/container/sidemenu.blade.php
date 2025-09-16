
<?php
    $text = $attributes['x-text'];
    $icon = $attributes['x-icon'];
    $url = $attributes['x-url'];
    $label_trans_id = $attributes['x-label-trans-id'];
    $badges = strval($attributes['badges'] ?? '[]');//json_decode( strval($attributes['badges'] ?? '[]') );  // done in v-bind:badges
    $badges = json_decode(html_entity_decode($badges)); //convert html entities into appropriate characters

?>

<li class="nav-item">
    <a href="<?=$url?>" class="nav-link m-0">
        <i class="nav-icon fas <?=$icon?>"></i>
        <p <?=$attributes?>>
            <span class="m-0" label-trans-id="<?=$label_trans_id?>"> {!! \iProtek\Pay\Helpers\LanguageHelper::get_translation(  $label_trans_id, $text)  !!} </span> 
            <?php for($i = 0; $i < count($badges); $i++){
                $badge = $badges[$i];
            ?>
                <span class="right badge <?=$badge->class?>"> <?=$badge->text?> </span>
            <?php } ?>
        </p>
    </a>
</li>
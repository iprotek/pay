    <!-- /.login-logo -->
<?php
    $title = $attributes['x-title'];
    $type = $attributes['x-type'];
?>

    <div class="card card-outline card-<?=$type?>" <?=$attributes?>>
        <div class="card-header text-center">
            <a <?=$attributes?>><b><?=$title?></b></a>
        </div>
        <div class="card-body">
            <?=$slot?>
        </div>
        <!-- /.card-body -->
    </div>
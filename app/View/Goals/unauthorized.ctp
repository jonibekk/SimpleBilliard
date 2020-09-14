<?php?>
<div class="jumbotron jumbotron-icon text-center">
    <i class="fa-flag fa fa-5"></i>

    <p><?= __("You do not have permission to view this goal") ?></p>

    <div class="text-left">
        <h3><?= __("Groups that can see this goal") ?></h3>
        <ul>
            <?php foreach ($groups as $group) : ?>
                <li><?= $group["name"] ?></li>
            <?php endforeach ?>
        </ul>
    </div>
</div>

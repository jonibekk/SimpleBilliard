<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 5/28/14
 * Time: 5:04 PM
 *
 * @var $title_for_layout string
 * @var $this             View
 * @var $nav_disable
 */
?>
<div class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <a href="/" class="navbar-brand"><?= $title_for_layout ?></a>
        </div>
        <div class="navbar-collapse collapse" id="navbar-main">
            <?
            if (!$nav_disable) {
                echo $this->element('navbar');
            }
            ?>
        </div>
    </div>
</div>

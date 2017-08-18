<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 5/28/14
 * Time: 5:04 PM
 *
 * @var CodeCompletionView $this
 * @var                    $title_for_layout string
 * @var                    $this             View
 * @var                    $nav_disable
 */
?>
<?= $this->App->viewStartComment()?>
<div class="navbar navbar-fixed-top navbar-default gl-navbar h_50px" id="header" style="box-shadow: none;">
    <div class="container">
        <div class="nav-container header-container">
            <a class="logo-title" href="/">
                <?= $this->Html->image('homepage/Goalous_logo.png', array('alt' => 'Goalous', 'height' => '30')); ?>
            </a>
        </div>
    </div>
</div>
<?= $this->App->viewEndComment()?>

<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 5/28/14
 * Time: 5:24 PM
 *
 * @var $title_for_layout string
 * @var $this             View
 * @var $nav_disable
 */
?>
<div class="navbar-collapse collapse" id="navbar-main">
    <ul class="nav navbar-nav">
        <li><?=
            $this->Html->link(__d('home', 'Blog'), 'http://blog.goalous.com/',
                              ['target' => '_blank']) ?></li>
        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" href="#" id="download"><?= __d('gl', "ヘルプ") ?>
                <span class="caret"></span></a>
            <ul class="dropdown-menu" aria-labelledby="download">
                <li><a href="#" data-toggle="modal" data-target="#modal_tutorial"><?= __d('gl', "チュートリアル") ?></a></li>
            </ul>
        </li>
    </ul>

    <ul class="nav navbar-nav navbar-right">
        <li><?= $this->Html->link(__d('gl', "ログアウト"), ['controller' => 'users', 'action' => 'logout']) ?></li>
    </ul>
</div>
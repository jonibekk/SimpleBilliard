<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 5/28/14
 * Time: 5:24 PM

 *
*@var $title_for_layout string
 * @var $nav_disable
 * @var $this             CodeCompletionView
 */
?>
<div class="navbar-collapse collapse" id="navbar-main">
    <ul class="nav navbar-nav">
    </ul>

    <ul class="nav navbar-nav navbar-right">
        <li class="dropdown">
            <a href="#" class="dropdown-toggle me-menu-image" data-toggle="dropdown" href="#" id="download">
                <?=
                $this->Upload->uploadImage($this->Session->read('Auth'), 'User.photo', ['style' => 'medium'],
                                           ['width' => '40px', 'height' => '40px', 'class' => 'img-circle']) ?>
                <span class="caret"></span></a>
            <ul class="dropdown-menu" aria-labelledby="download">
                <li><a href="#" data-toggle="modal" data-target="#modal_tutorial"><?= __d('gl', "チュートリアル") ?></a></li>
                <li><?= $this->Html->link(__d('gl', "ログアウト"), ['controller' => 'users', 'action' => 'logout']) ?></li>
                <li class="divider"></li>
                <li><?=
                    $this->Html->link(__d('home', 'Blog'), 'http://blog.goalous.com/',
                                      ['target' => '_blank']) ?></li>
            </ul>
        </li>
    </ul>
</div>
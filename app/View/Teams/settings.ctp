<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/18/14
 * Time: 5:40 PM
 *
 * @var CodeCompletionView $this
 */
?>
<!-- START app/View/Teams/settings.ctp -->
<? $this->start('sidebar') ?>
<li class="active"><a href="#account"><?= __d('gl', "メンバー招待") ?></a></li>
<li class=""><a href="#profile"><?= __d('gl', "一括登録") ?></a></li>
<? $this->end() ?>
<div id="account">
    <?= $this->element('Team/invite') ?>
</div>
<div id="profile">
    <?= $this->element('Team/batch_setup') ?>
</div>
<!-- END app/View/Teams/settings.ctp -->

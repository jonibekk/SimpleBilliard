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
<? $this->start('sidebar') ?>
<li class="active"><a href="#account"><?= __d('gl', "アカウント") ?></a></li>
<li class=""><a href="#profile"><?= __d('gl', "プロフィール") ?></a></li>
<li class=""><!--suppress HtmlUnknownAnchorTarget -->
    <a href="#notification"><?= __d('gl', "通知") ?></a></li>
<li class=""><!--suppress HtmlUnknownAnchorTarget -->
    <a href="#link"><?= __d('gl', "リンク") ?></a></li>
<? $this->end() ?>
<div id="account">
    <?= $this->element('User/account_setting') ?>
</div>
<div id="profile">
    <?= $this->element('User/profile_setting') ?>
</div>
<!--<div id="notification">-->
<!---->
<!--</div>-->
<!--<div id="link">-->

<!--</div>-->

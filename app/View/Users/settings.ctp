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
<!-- START app/View/Users/settings.ctp -->
<? $this->start('sidebar') ?>
<li class="active"><a href="#account"><?= __d('gl', "アカウント") ?></a></li>
<li class=""><a href="#profile"><?= __d('gl', "プロフィール") ?></a></li>
<li class=""><!--suppress HtmlUnknownAnchorTarget -->
    <a class="develop--forbiddenLink" href="#notification"><?= __d('gl', "通知") ?></a></li>
<li class=""><!--suppress HtmlUnknownAnchorTarget -->
    <a class="develop--forbiddenLink" href="#link"><?= __d('gl', "リンク") ?></a></li>
<? $this->end() ?>
<div id="account">
    <?= $this->element('User/account_setting') ?>
</div>
<div id="profile">
    <?= $this->element('User/profile_setting') ?>
</div>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script>
    $(function () {
        $(".develop--forbiddenLink").hover(
            function () {
                $(this).append($('<div class="develop--forbiddenLink__design">準備中です</div>'));
            },
            function () {
                $(this).find("div:last").remove();
            }
        );
    });
</script>

<!--<div id="notification">-->
<!---->
<!--</div>-->
<!--<div id="link">-->

<!--</div>-->
<!-- END app/View/Users/settings.ctp -->

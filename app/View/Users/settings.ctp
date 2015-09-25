<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/18/14
 * Time: 5:40 PM
 *
 * @var CodeCompletionView $this
 */

// two_column レイアウトで、xxs サイズの時にサイドバーを隠す
$this->set('hidden_sidebar_xxs', true);
?>
<!-- START app/View/Users/settings.ctp -->
<?php $this->start('sidebar') ?>
<div class="sidebar-setting" role="complementary">
    <ul class="nav">
        <li class="active"><a href="#account"><?= __d('gl', "アカウント") ?></a></li>
        <li class=""><a href="#profile"><?= __d('gl', "プロフィール") ?></a></li>
        <li class="">
            <a class="" href="#notification"><?= __d('gl', "通知") ?></a></li>
        <li class=""><!--suppress HtmlUnknownAnchorTarget -->
            <a class="develop--forbiddenLink" href="#link"><?= __d('gl', "リンク") ?></a></li>
    </ul>
</div>
<?php $this->end() ?>
<div id="account">
    <?= $this->element('User/account_setting') ?>
</div>
<div id="profile">
    <?= $this->element('User/profile_setting') ?>
</div>
<div id="notification">
    <?= $this->element('User/notify_setting') ?>
</div>
<!--<div id="link">-->

<!--</div>-->
<?php $this->append('script'); ?>
<script type="text/javascript">
    $(document).ready(function () {
        $('body').scrollspy({target: '.sidebar-setting'});
    });
</script>
<?php $this->end(); ?>
<!-- END app/View/Users/settings.ctp -->

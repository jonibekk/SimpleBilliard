<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/18/14
 * Time: 5:40 PM.
 *
 * @var CodeCompletionView
 */

// two_column レイアウトで、xxs サイズの時にサイドバーを隠す
$this->set('hidden_sidebar_xxs', true);
?>
<?= $this->App->viewStartComment()?>
<?php $this->start('sidebar') ?>
<div class="sidebar-setting" role="complementary" id="SidebarSetting">
    <ul class="nav">
        <li class="active"><a href="#account"><?= __('Account') ?></a></li>
        <li class=""><a href="#profile"><?= __('Profile') ?></a></li>
        <li class=""><a class="" href="#notification"><?= __('Notification') ?></a></li>
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
<?= $this->App->viewEndComment()?>
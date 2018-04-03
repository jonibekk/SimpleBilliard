<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 5/22/14
 * Time: 6:47 PM
 *
 * @var CodeCompletionView $this
 * @var                    $current_circle
 * @var                    $my_member_status
 * @var                    $is_mb_app
 * @var                    $notify_setting
 * @var                    $unread_msg_topic_ids
 */
?>
<?= $this->App->viewStartComment() ?>
<?= $this->element('cake_variables') ?>
<?= $this->element('google_tag_manager', ['page_type' => 'app']) ?>

<?php

// Include page specific javascript file
if (isset($page_js_files) && !empty($page_js_files)) {
    foreach ($page_js_files as $script) {
        echo $this->Html->script($script);
    }
}

// Loading angular app js for only team members / team visions / group visions page
if ($this->request->params['controller'] === 'teams' && $this->request->params['action'] === 'main') {
    echo $this->Html->script('/js/ng_vendors.min');
    echo $this->Html->script('/js/ng_app.min');
}

echo $this->Html->script('/js/vendors.min');
echo $this->Html->script('/js/goalous.min');
echo $this->PageResource->getPageScript();
?>

<?php //公開環境のみタグを有効化
if (PUBLIC_ENV) {
    /** @noinspection PhpDeprecationInspection */
    // TODO: Find a more optimized solution to track user interactions
    echo $this->element('intercom');
}
?>

<?= $this->Session->flash('click_event') ?>
<?php echo $this->Session->flash('noty');
//環境を識別できるようにリボンを表示
?>
<?php if (ENV_NAME == "stg"): ?>
    <p class="ribbon ribbon-staging">Staging</p>
<?php elseif (ENV_NAME == "hotfix"): ?>
    <p class="ribbon ribbon-hotfix">Hotfix</p>
<?php elseif (ENV_NAME == "dev"): ?>
    <p class="ribbon ribbon-develop">Develop</p>
<?php elseif (ENV_NAME == "dev-ind"): ?>
    <p class="ribbon ribbon-dev-india">Dev India</p>
<?php elseif (ENV_NAME == "local"): ?>
    <p class="ribbon ribbon-local">Local</p>
<?php endif; ?>

<?php if(isset($mode_view) && $mode_view == MODE_VIEW_TUTORIAL):?>
<script>
    $(document).ready(function () {
        $("#modal_tutorial").modal('show');
    });
</script>
<?php endif;?>

<?php
// Comment out to load stripe.js temporarily because this error occurred when signup or other page. 「Uncaught DOMException: Blocked a frame with origin "https://dev.goalous.com" from accessing a cross-origin frame. 」
// <!-- Required by Stripe.js API to appear on every page for security reasons -->
// <!--<script src="https://js.stripe.com/v3/"></script>-->
?>

<?= $this->App->viewEndComment() ?>

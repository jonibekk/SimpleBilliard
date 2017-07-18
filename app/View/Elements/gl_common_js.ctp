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
// right column react app
if (!empty($loadRightColumn)) {
    echo $this->Html->script('/js/react_kr_column_app.min', ['defer' => 'defer']);
}

// Include page specific javascript file
if (isset($page_js_files) && !empty($page_js_files)) {
    foreach ($page_js_files as $script) {
        echo $this->Html->script($script);
    }
}
echo $this->Html->script('/js/vendors.min');
echo $this->Html->script('/js/goalous.min');
echo $this->PageResource->getPageScript();
?>

<?php //公開環境のみタグを有効化
if (PUBLIC_ENV) {
    /** @noinspection PhpDeprecationInspection */
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

<?= $this->App->viewEndComment() ?>

<?php
/**
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 2014/07/19
 * Time: 23:28
 *
 * @var CodeCompletionView $this
 * @var                    $circle
 * @var                    $key
 * @var                    $form
 * @var                    $admin
 * @var                    $joined
 * @var                    $member_count
 */
if (!isset($form)) {
    $form = true;
}
$admin = isset($admin) ? $admin : false;
$joined = isset($joined) ? $joined : false;
$member_count = isset($member_count) ? $member_count : '';
?>
<?= $this->App->viewStartComment() ?>
<div class="col col-xxs-12 mpTB0 circle-item-row">
    <?=
    $this->Upload->uploadImage($circle, 'Circle.photo', ['style' => 'medium_large'],
        ['class' => 'comment-img'])
    ?>
    <div class="comment-body modal-comment">
        <?php if ($form): ?>
            <div class="pull-right circle-join-switch">
                <?php if ($admin): ?>
                    <?= __("Administrators") ?>
                <?php elseif ($circle['Circle']['team_all_flg']): ?>
                    <?php // チーム全体サークルは変更不可 ?>
                <?php else: ?>
                    <?= $this->Form->input("$key.join",
                        [
                            'label'       => false,
                            'div'         => false,
                            'type'        => 'checkbox',
                            'class'       => 'bt-switch',
                            'default'     => $joined ? true : false,
                            'data-id'     => $circle['Circle']['id'],
                            'data-secret' => $circle['Circle']['public_flg'] ? "0" : "1"
                        ]) ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <div class="modal-public-circles-contents-circle-name font_12px font_bold modalFeedTextPadding">
            <?php if ($circle['Circle']['created'] > strtotime("-1 week")): ?>
                <span class="circle-item-new">New</span>
            <?php endif; ?>
            <?php if (!$circle['Circle']['public_flg']): ?>
                <i class="fa fa-lock circle-item-secret-mark"></i>
            <?php endif ?>
            <a href="<?= "/circles/" . $circle['Circle']['id'] ."/posts"?>"
               class="link-dark-gray circle-item-circle-link">
                <p class="circle-item-circle-name"><?= h($circle['Circle']['name']) ?></p>
            </a>
        </div>
        <div class="font_12px font_lightgray modalFeedTextPaddingSmall">
            <a href="#"
               data-url="<?= $this->Html->url([
                   'controller' => 'circles',
                   'action'     => 'ajax_get_circle_members',
                   'circle_id'  => $circle['Circle']['id']
               ]) ?>"
               class="modal-ajax-get remove-on-hide">
                <?= __("%s members", $member_count) ?>
            </a>
            &middot;
            <?= $this->TimeEx->elapsedTime(h($circle['Circle']['modified']), 'rough') ?>
        </div>
    </div>
</div>
<?= $this->App->viewEndComment() ?>

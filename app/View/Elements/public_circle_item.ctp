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
<!-- START app/View/Elements/public_circle_item.ctp -->
<div class="col col-xxs-12 mpTB0">
    <?=
    $this->Upload->uploadImage($circle, 'Circle.photo', ['style' => 'small'],
                               ['class' => 'comment-img'])
    ?>
    <div class="comment-body modal-comment">
        <?php if ($form): ?>
            <div class="pull-right circle-join-switch">
                <?php if ($admin): ?>
                    <?= __d('gl', "管理者") ?>
                <?php elseif ($circle['Circle']['team_all_flg']): ?>
                    <?php // チーム全体サークルは変更不可 ?>
                <?php else: ?>
                    <?= $this->Form->input("$key.join",
                                           ['label'       => false,
                                            'div'         => false,
                                            'type'        => 'checkbox',
                                            'class'       => 'bt-switch',
                                            'default'     => $joined ? true : false,
                                            'data-secret' => $circle['Circle']['public_flg'] ? "0" : "1"]) ?>
                    <?= $this->Form->hidden("$key.circle_id", ['value' => $circle['Circle']['id']]) ?>
                <?php endif; ?>
            </div>
            <?php if (!$circle['Circle']['public_flg']): ?>
                <div class="pull-right circle-item-secret-mark" style="">
                    <i class="fa fa-lock"></i>
                </div>
            <?php endif ?>
        <?php endif; ?>
        <div class="font_12px font_bold modalFeedTextPadding">
            <?php if ($circle['Circle']['created'] > strtotime("-1 week")): ?>
                <span class="circle-new">New</span>
            <?php endif; ?>
            <a href="<?= $this->Html->url(['controller' => 'posts', 'action' => 'feed', 'circle_id' => $circle['Circle']['id']]) ?>"
               class="link-dark-gray">
                <?= h($circle['Circle']['name']) ?>
            </a>
        </div>
        <div class="font_12px font_lightgray modalFeedTextPaddingSmall">
            <a href="<?= $this->Html->url(['controller' => 'circles', 'action' => 'ajax_get_circle_members', 'circle_id' => $circle['Circle']['id']]) ?>"
               class="modal-ajax-get">
                <?= __d('gl', "%s メンバー", $member_count) ?>
            </a>
            &middot;
            <?= $this->TimeEx->elapsedTime(h($circle['Circle']['modified']), 'rough') ?>
        </div>
    </div>
</div>
<!-- END app/View/Elements/public_circle_item.ctp -->

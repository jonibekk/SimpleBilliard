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
 */
if (!isset($form)) {
    $form = true;
}
?>
<!-- START app/View/Elements/public_circle_item.ctp -->
<div class="col col-xxs-12 mpTB0">
    <?=
    $this->Upload->uploadImage($circle, 'Circle.photo', ['style' => 'small'],
                               ['class' => 'comment-img'])
    ?>
    <div class="comment-body modal-comment">
        <? if ($form): ?>
            <div class="pull-right circle-join-switch">
                <? if (!empty($circle['CircleAdmin'])): ?>
                    <?= __d('gl', "管理者") ?>
                <? else: ?>
                    <?
                    $joined = false;
                    foreach ($circle['CircleMember'] as $member) {
                        if ($member['user_id'] == $this->Session->read('Auth.User.id')) {
                            $joined = true;
                            break;
                        }
                    }
                    echo $this->Form->input("$key.join",
                                            ['label' => false, 'div' => false, 'type' => 'checkbox', 'class' => 'bt-switch', 'default' => $joined ? true : false]) ?>
                    <?= $this->Form->hidden("$key.circle_id", ['value' => $circle['Circle']['id']]) ?>
                <? endif; ?>
            </div>
        <? endif; ?>
        <div class="font_12px font_bold modalFeedTextPadding">
            <? if ($circle['Circle']['created'] > strtotime("-1 week")): ?>
                <span class="circle-new">New</span>
            <? endif; ?>
            <?= h($circle['Circle']['name']) ?>
        </div>
        <div class="font_12px font_lightgray modalFeedTextPaddingSmall">
            <?= __d('gl', "%s メンバー", $circle['Circle']['circle_member_count']) ?>
            &middot;
            <?= $this->TimeEx->elapsedTime(h($circle['Circle']['modified']), 'rough') ?>
        </div>
    </div>
</div>
<!-- END app/View/Elements/public_circle_item.ctp -->

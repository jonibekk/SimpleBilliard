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
                               ['class' => 'gl-comment-img'])
    ?>
    <div class="gl-comment-body gl-modal-comment">
        <? if ($form): ?>
            <div class="pull-right gl-circle-join-switch">
                <? if ($circle['CircleMember'][0]['admin_flg']): ?>
                    <?= __d('gl', "管理者の為、変更不可") ?>
                <? else: ?>
                    <?=
                    $this->Form->input("$key.join",
                                       ['label' => false, 'div' => false, 'type' => 'checkbox', 'class' => 'bt-switch', 'default' => !empty($circle['CircleMember']) ? true : false]) ?>
                    <?= $this->Form->hidden("$key.circle_id", ['value' => $circle['Circle']['id']]) ?>
                <?endif; ?>
            </div>
        <? endif; ?>
        <div class="font-size_12 font-weight_bold modalFeedTextPadding">
            <?= h($circle['Circle']['name']) ?></div>

        <div class="font-size_12 color9197a3 modalFeedTextPaddingSmall">
            <?
            $title = '<ul class="gl-user-list-in-tooltip">';
            foreach ($circle['CircleMember'] as $member) {
                $img = $this->Upload->uploadImage($member, 'User.photo', ['style' => 'small'],
                                                  ['width' => '16px', 'height' => '16px']);
                $username = $member['User']['display_username'];
                $title .= "<li>{$img}&nbsp;{$username}</li>";
            }
            $title .= "</ul>";
            ?>
            <a href="#" data-triger="click" data-toggle="tooltip" data-placement="bottom" data-html="true"
               data-original-title='<?= $title ?>'>
                <?= __d('gl', "%s人のメンバーが所属", $circle['Circle']['circle_member_count']) ?></a>
        </div>
    </div>
</div>
<!-- END app/View/Elements/public_circle_item.ctp -->

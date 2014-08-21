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
                <?endif; ?>
            </div>
        <? endif; ?>
        <div class="font-size_12 font-weight_bold modalFeedTextPadding">
            <?= h($circle['Circle']['name']) ?></div>

        <div class="font-size_12 font-lightgray modalFeedTextPaddingSmall">
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
                <?= __d('gl', "%s メンバー", $circle['Circle']['circle_member_count']) ?></a>
        </div>
    </div>
</div>
<!-- END app/View/Elements/public_circle_item.ctp -->

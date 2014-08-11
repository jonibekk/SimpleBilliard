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
 */
?>
<!-- START app/View/Elements/public_circle_item.ctp -->
<div class="col col-xxs-12 mpTB0">
    <?=
    $this->Upload->uploadImage($circle, 'Circle.photo', ['style' => 'small'],
                               ['class' => 'gl-comment-img'])
    ?>
    <div class="gl-comment-body gl-modal-comment">
        <div class="pull-right">
            <?=
            $this->Form->input("$key.join",
                               ['label' => false, 'type' => 'checkbox', 'class' => 'bt-switch', 'default' => !empty($circle['CircleMember']) ? true : false]) ?>
            <?= $this->Form->hidden("$key.circle_id", ['value' => $circle['Circle']['id']]) ?>
        </div>
        <div class="font-size_12 font-weight_bold modalFeedTextPadding">
            <?= h($circle['Circle']['name']) ?></div>

        <div class="font-size_12 color9197a3 modalFeedTextPaddingSmall">
            <?= __d('gl', "%s人のメンバーが所属", $circle['Circle']['circle_member_count']) ?>
        </div>
    </div>
</div>
<!-- END app/View/Elements/public_circle_item.ctp -->

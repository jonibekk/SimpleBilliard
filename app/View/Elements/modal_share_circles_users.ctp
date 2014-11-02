<?php
/**
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 2014/07/19
 * Time: 22:11
 *
 * @var                    $circles
 * @var                    $users
 * @var CodeCompletionView $this
 * @var                    $total_share_user_count
 */
?>
<!-- START app/View/Elements/modal_share_circles_users.ctp -->
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true"><span
                    class="close-icon">&times;</span></button>
            <h4 class="modal-title font_18px font_bold"><?= __d('gl', "%s人に共有しました", $total_share_user_count) ?></h4>
        </div>
        <div class="modal-body modal-feed-body">
            <div class="row borderBottom">
                <? if (!empty($circles)): ?>
                    <? foreach ($circles as $key => $circle): ?>
                        <?=
                        $this->element('public_circle_item', ['circle' => $circle, 'key' => $key, 'form' => false]) ?>
                    <? endforeach ?>
                <? endif ?>
                <? if (!empty($users)): ?>
                    <? foreach ($users as $user): ?>
                        <?=
                        $this->element('Feed/read_like_user',
                                       ['user' => $user['User'], 'created' => null]) ?>
                    <? endforeach ?>
                <? endif; ?>
            </div>
        </div>
        <div class="modal-footer modal-feed-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= __d('gl', "閉じる") ?></button>
        </div>
    </div>
</div>
<!-- END app/View/Elements/modal_share_circles_users.ctp -->

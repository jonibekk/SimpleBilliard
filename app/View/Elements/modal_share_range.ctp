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
<!-- START app/View/Elements/modal_share_range.ctp -->
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true"><span
                    class="close-icon">&times;</span></button>
            <h4 class="modal-title font_18px font_bold"><?= __d('gl', "%s人に共有しました", $total_share_user_count) ?></h4>
        </div>
        <div class="modal-body modal-feed-body">
            <div class="row borderBottom">
                <?php if (!empty($circles)): ?>
                    <?php foreach ($circles as $key => $circle): ?>
                        <?=
                        $this->element('public_circle_item', ['circle' => $circle, 'key' => $key, 'form' => false]) ?>
                    <?php endforeach ?>
                <?php endif ?>
                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $user): ?>
                        <?=
                        $this->element('Feed/read_like_user',
                                       ['user' => $user['User'], 'created' => null]) ?>
                    <?php endforeach ?>
                <?php endif; ?>
            </div>
        </div>
        <div class="modal-footer modal-feed-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= __d('gl', "閉じる") ?></button>
        </div>
    </div>
</div>
<!-- END app/View/Elements/modal_share_range.ctp -->
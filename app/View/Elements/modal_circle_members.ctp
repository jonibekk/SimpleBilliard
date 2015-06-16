<?php
/**
 * Created by PhpStorm.
 * User: t-tsunekawa
 * Date: 2015/04/3
 * Time: 3:19 PM
 *
 * @var View
 * @var $circle_members
 */
?>
<!-- START app/View/Elements/modal_circle_members.ctp -->
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true"><span
                    class="close-icon">&times;</span></button>
            <h4 class="modal-title font_18px font_bold"><?= __d('gl', "このサークルのメンバー(%s)",
                                                                count($circle_members)) ?></h4>
        </div>
        <div class="modal-body modal-feed-body">
            <?php if (!empty($circle_members)): ?>
                <div class="row borderBottom">
                    <?php foreach ($circle_members as $user): ?>
                        <?=
                        $this->element('Feed/read_like_user',
                                       ['user' => $user['User'], 'created' => $user['CircleMember']['modified'], 'type' => 'rough']) ?>
                    <?php endforeach ?>
                </div>
            <?php else: ?>
                <?= __d('gl', "このサークルにはメンバーがいません。") ?>
            <?php endif ?>
        </div>
        <div class="modal-footer modal-feed-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= __d('gl', "閉じる") ?></button>
        </div>
    </div>
</div>
<!-- END app/View/Elements/modal_circle_members.ctp -->

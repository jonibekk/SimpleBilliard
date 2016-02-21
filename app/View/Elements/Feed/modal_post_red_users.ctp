<?php
/**
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 2014/07/19
 * Time: 22:11
 *
 * @var                    $red_users
 * @var CodeCompletionView $this
 */
?>
<!-- START app/View/Elements/Feed/modal_post_red_users.ctp -->
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true"><span
                    class="close-icon">&times;</span></button>
            <h4 class="modal-title font_18px font_bold"><?= __d('app', "この投稿を%s人が読みました。",
                                                                count($red_users)) ?></h4>
        </div>
        <div class="modal-body modal-feed-body">
            <?php if (!empty($red_users)): ?>
                <div class="row borderBottom">
                    <?php foreach ($red_users as $user): ?>
                        <?=
                        $this->element('Feed/read_like_user',
                                       ['user' => $user['User'], 'created' => $user['PostRead']['created']]) ?>
                    <?php endforeach ?>
                </div>
            <?php else: ?>
                <?= __d('app', "まだ、この投稿を読んだ人はいません。") ?>
            <?php endif ?>
        </div>
        <div class="modal-footer modal-feed-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= __d('app', "閉じる") ?></button>
        </div>
    </div>
</div>
<!-- END app/View/Elements/Feed/modal_post_red_users.ctp -->
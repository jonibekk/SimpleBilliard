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
<!-- START app/View/Elements/Feed/modal_comment_red_users.ctp -->
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true"><span
                    class="close-icon">&times;</span></button>
            <h4 class="modal-title"><?= __d('gl', "このコメントを%s人が読みました。", count($red_users)) ?></h4>
        </div>
        <div class="modal-body modal-feed-body">
            <?php if (!empty($red_users)): ?>
                <div class="row borderBottom">
                    <?php foreach ($red_users as $user): ?>
                        <?=
                        $this->element('Feed/read_like_user',
                                       ['user' => $user['User'], 'created' => $user['CommentRead']['created']]) ?>
                    <?php endforeach ?>
                </div>
            <?php else: ?>
                <?= __d('gl', "まだ、この投稿を読んだ人はいません。") ?>
            <?php endif ?>
        </div>
        <div class="modal-footer modal-feed-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= __d('gl', "閉じる") ?></button>
        </div>
    </div>
</div>
<!-- END app/View/Elements/Feed/modal_comment_red_users.ctp -->

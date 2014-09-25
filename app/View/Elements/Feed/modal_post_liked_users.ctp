<?php
/**
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 2014/07/19
 * Time: 22:11
 *
 * @var                    $liked_users
 * @var CodeCompletionView $this
 */
?>
<!-- START app/View/Elements/Feed/modal_post_liked_users.ctp -->
<div class="modal-dialog">
    <div class="modal-content modalFeed-content">
        <div class="modal-header modalFeed-header">
            <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true"><span
                    class="close-icon">&times;</span></button>
            <h4 class="modal-title font_18px font_bold"><?= __d('gl', "この投稿を%s人が「いいね！」と言っています。",
                                                                          count($liked_users)) ?></h4>
        </div>
        <div class="modal-body modalFeed-body">
            <? if (!empty($liked_users)): ?>
                <div class="row borderBottom">
                <? foreach ($liked_users as $user): ?>
                        <?=
                        $this->element('Feed/read_like_user',
                                       ['user' => $user['User'], 'created' => $user['PostLike']['created']]) ?>
                    <? endforeach ?>
                </div>
            <? else: ?>
                <?= __d('gl', "まだ、いいね！と言っている人はいません。") ?>
            <?endif ?>
        </div>
        <div class="modal-footer modalFeed-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?= __d('gl', "閉じる") ?></button>
        </div>
    </div>
</div>
<!-- END app/View/Elements/Feed/modal_post_liked_users.ctp -->
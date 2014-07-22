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
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title"><?= __d('gl', "この投稿に「いいね！」と言っている人") ?></h4>
        </div>
        <div class="modal-body">
            <? if (!empty($liked_users)): ?>
                <div class="row">
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
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= __d('gl', "閉じる") ?></button>
        </div>
    </div>
</div>

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
        <div class="modal-header modalFeed-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title font-size_18 font-weight_bold"><?= __d('gl', "この投稿を%s人が読みました。",
                                                                          count($red_users)) ?></h4>
        </div>
        <div class="modal-body modalFeed-body">
            <? if (!empty($red_users)): ?>
                <div class="row borderBottom">
                <? foreach ($red_users as $user): ?>
                        <?=
                        $this->element('Feed/read_like_user',
                                       ['user' => $user['User'], 'created' => $user['PostRead']['created']]) ?>
                    <? endforeach ?>
                </div>
            <? else: ?>
                <?= __d('gl', "まだ、この投稿を読んだ人はいません。") ?>
            <?endif ?>
        </div>
        <div class="modal-footer modalFeed-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?= __d('gl', "閉じる") ?></button>
        </div>
    </div>
</div>
<!-- END app/View/Elements/Feed/modal_post_red_users.ctp -->
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
<?= $this->App->viewStartComment()?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true"><span
                    class="close-icon">&times;</span></button>
            <h4 class="modal-title"><?= __("%s people read this comment.", count($red_users)) ?></h4>
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
                <?= __("No one read this comment.") ?>
            <?php endif ?>
        </div>
        <div class="modal-footer modal-feed-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= __("Close") ?></button>
        </div>
    </div>
</div>
<?= $this->App->viewEndComment()?>

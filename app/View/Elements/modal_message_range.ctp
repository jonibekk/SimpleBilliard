<?php
/**
 * Created by PhpStorm.
 *
 * @var                    $users
 * @var CodeCompletionView $this
 * @var                    $total_share_user_count
 */
?>
<?= $this->App->viewStartComment()?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true"><span
                    class="close-icon">&times;</span></button>
            <h4 class="modal-title font_18px font_bold"><?= __("Members in this topic", $total_share_user_count) ?></h4>
        </div>
        <div class="modal-body modal-feed-body">
            <div class="row borderBottom">
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
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= __("Close") ?></button>
        </div>
    </div>
</div>
<?= $this->App->viewEndComment()?>

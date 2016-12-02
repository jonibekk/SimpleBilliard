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
<?= $this->App->viewStartComment()?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true"><span
                    class="close-icon">&times;</span></button>
            <h4 class="modal-title font_18px font_bold">
                <?= __("Like!") ?>(<?= count($liked_users) ?>)
            </h4>
        </div>
        <div class="modal-body without-footer">
            <?php if (!empty($liked_users)): ?>
                <div class="row borderBottom">
                    <?php foreach ($liked_users as $user): ?>
                        <?=
                        $this->element('Feed/read_like_user',
                            ['user' => $user['User'], 'created' => $user['PostLike']['created']]) ?>
                    <?php endforeach ?>
                </div>
            <?php else: ?>
                <?= __("There is no like! yet.") ?>
            <?php endif ?>
        </div>
    </div>
</div>
<?= $this->App->viewEndComment()?>

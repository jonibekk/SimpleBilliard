<?php
/**
 * Created by PhpStorm.
 * User: saeki
 * Date: 15/04/17
 * Time: 16:56
 *
 * @var CodeCompletionView $this
 * @var                    $user
 */
?>
<?= $this->App->viewStartComment()?>
<div class="col col-xxs-12 mpTB0">
    <?=
    $this->Upload->uploadImage($user, 'User.photo', ['style' => 'small'],
        ['class' => 'comment-img'])
    ?>
    <div class="comment-body modal-comment" style="margin-top:5px;">
        <div class="font_12px font_bold modalFeedTextPadding">
            <?= h($user['display_username']) ?>
        </div>
    </div>
</div>
<?= $this->App->viewEndComment()?>

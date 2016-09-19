<?php
/**
 * Created by PhpStorm.
 * User: saeki
 * Date: 15/04/17
 * Time: 15:07
 *
 * @var CodeCompletionView $this
 * @var                    $user
 * @var                    $evaluate_term_id
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
            <a class="modal-ajax-get pointer"
               href="<?= $this->Html->url(['controller'       => 'evaluations',
                                           'action'           => 'ajax_get_evaluatees_by_evaluator',
                                           'user_id'          => $user['id'],
                                           'evaluate_term_id' => $evaluate_term_id
               ]) ?>">
                <?= __("View details") ?>
            </a>
        </div>
        <div class="font_12px modalFeedTextPadding">
            <?= __("Remaining") ?> <?= h($user['incomplete_count']) ?>
        </div>
    </div>
</div>
<?= $this->App->viewEndComment()?>

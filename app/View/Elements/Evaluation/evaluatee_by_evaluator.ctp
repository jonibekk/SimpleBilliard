<?php
/**
 * Created by PhpStorm.
 * User: saeki
 * Date: 15/04/17
 * Time: 15:44
 *
 * @var CodeCompletionView $this
 * @var                    $user
 * @var                    $evaluate_term_id
 */
?>
<?= $this->App->viewStartComment()?>
<div class="col col-xxs-12 mpTB0">
    <?=
    $this->Upload->uploadImage($user['User'], 'User.photo', ['style' => 'small'],
        ['class' => 'comment-img'])
    ?>
    <div class="comment-body modal-comment" style="margin-top:5px;">
        <div class="font_12px font_bold modalFeedTextPadding">
            <?= h($user['User']['display_username']) ?>
            <a class="modal-ajax-get pointer"
               href="<?= $this->Html->url([
                   'controller'       => 'evaluations',
                   'action'           => 'ajax_get_evaluators_status',
                   'evaluate_term_id' => $evaluate_term_id,
                   'user_id'          => $user['User']['id']
               ]) ?>">
                <?= __("View details") ?>
            </a>
        </div>
        <div class="font_12px modalFeedTextPadding">
            <?= h($user['Evaluation']['evaluator_type_name']) ?>
        </div>
    </div>
</div>
<?= $this->App->viewEndComment()?>

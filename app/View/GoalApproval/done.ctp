<?php /**
 * @var CodeCompletionView $this
 * @var                    $unapproved_cnt
 * @var                    $done_cnt
 * @var                    $goal_info
 * @var                    $value_unit_list
 */
?>
<?= $this->App->viewStartComment()?>
<style type="text/css">
    .approval_body_text {
        font-size: 14px
    }

    .sp-feed-alt-sub {
        background: #f5f5f5;
        position: fixed;
        top: 50px;
        z-index: 1;
        box-shadow: 0 2px 4px rgba(0, 0, 0, .15);
        width: 100%;
        left: 0;
    }

    .approval_body_start_area {
        margin-top: 40px;
    }

    .approval_botton_area {
        text-align: center;
    }

    .approval_button {
        width: 97%;
        margin-bottom: 10px;
    }

    .approval_status_box {
        text-align: center;
        background-color: #696969;
        color: #ffffff;
        font-size: 12px;
        padding-top: 5px;
        padding-bottom: 5px;
        letter-spacing: 0.2em;
    }

    .approval_badge {
        margin: -20px 0 0 -4px;
        color: #fff;
        font-size: 10px;
        background-color: red;
    }

</style>

<div class="col col-md-12 sp-feed-alt-sub" style="top: 50px;" id="SpFeedAltSub">
    <div class="col col-xxs-6 text-align_r">
        <a class="font_lightGray-veryDark no-line plr_18px sp-feed-link inline-block pt_12px height_40px"
           id="SubHeaderMenuFeed"
           href="<?= $this->Html->url(['controller' => 'goal_approval', 'action' => 'index']) ?>">
            <?= __("Waiting") ?>
            <?php if ($unapproved_cnt > 0) { ?>
            <span class="btn btn-danger btn-xs bell-notify-box approval_badge">
            <?php echo $unapproved_cnt; ?>
            <?php } ?>
        </a>
    </div>
    <div class="col col-xxs-6">
        <a class="font_lightGray-veryDark no-line plr_18px sp-feed-link inline-block pt_12px height_40px sp-feed-active"
           id="SubHeaderMenuGoal">
            <?= __("Done") ?><?php if ($done_cnt > 0) {
                echo '(' . $done_cnt . ')';
            } ?></a>
    </div>
</div>

<div class="approval_body_start_area">
    <div class="row">
        <div class="col-sm-8 col-sm-offset-2">
            <?php if (isset($goal_info) === true && count($goal_info) > 0) { ?>
                <?php foreach ($goal_info as $goal) { ?>
                    <div class="panel panel-default" id="AddGoalFormPurposeWrap">

                        <?php if (isset($goal['status']) === true) { ?>
                            <div class="approval_status_box"><?= $goal['status']; ?></div>
                        <?php } ?>

                        <div class="panel-body goal-set-heading clearfix">

                            <p class="approval_body_text">
                                <?= $this->Html->image('ajax-loader.gif', [
                                    'class'         => 'lazy comment-img',
                                    'data-original' => $this->Upload->uploadUrl($goal['User'],
                                        'User.photo',
                                        ['style' => 'small'])
                                ]) ?></p>

                            <p class="approval_body_text"><?= __("Name") ?>
                                : <?= h($goal['User']['display_username']); ?></p>

                            <p class="approval_body_text"><?= __("Category") ?>
                                : <?= h($goal['Goal']['GoalCategory']['name']); ?></p>

                            <p class="approval_body_text"><?= __("Goal Name") ?>: <?= h($goal['Goal']['name']); ?></p>

                            <p class="approval_body_text"><?= $goal['Collaborator']['type'] === (string)Collaborator::TYPE_OWNER ?
                                    __("Leader") : __("Collaborator"); ?></p>

                            <p class="approval_body_text"><?= __("Role") ?>
                                : <?= h($goal['Collaborator']['role']); ?></p>

                            <p class="approval_body_text"><?= __("Due") ?>
                                : <?= $this->TimeEx->date(h($goal['Goal']['end_date'])) ?></p>

                            <p class="approval_body_text"><?= __("Weight") ?>
                                : <?= $goal['Collaborator']['priority']; ?></p>

                            <p class="approval_body_text"><?= __("Description") ?>
                                : <?= nl2br($this->TextEx->autoLink($goal['Goal']['description'])); ?></p>

                            <?=
                            $this->Html->image('ajax-loader.gif',
                                [
                                    'class'         => 'lazy',
                                    'data-original' => $this->Upload->uploadUrl($goal,
                                        "Goal.photo",
                                        ['style' => 'medium']),
                                    'width'         => '48px',
                                    'error-img'     => "/img/no-image-link.png",
                                ]
                            )
                            ?>
                        </div>

                        <div class="panel-body comment-block">
                            <?= $this->Form->create('GoalApproval',
                                [
                                    'id'         => 'GoalApprovalIndexForm_' . $goal['Collaborator']['id'],
                                    'url'        => ['controller' => 'goal_approval', 'action' => 'done'],
                                    'type'       => 'post',
                                    'novalidate' => true
                                ]); ?>
                            <?= $this->Form->hidden('collaborator_id', ['value' => $goal['Collaborator']['id']]); ?>

                            <?php if ($goal['is_present_term'] === true) { ?>
                                <div class="row">
                                    <div class="approval_botton_area">
                                        <?php if ($goal['my_goal'] === false) { ?>
                                            <?php if ($goal['Collaborator']['approval_status'] === (string)Collaborator::STATUS_APPROVAL) { ?>
                                                <?= $this->Form->button(__("Remove from target of evaluation"),
                                                    [
                                                        'name'  => 'wait_btn',
                                                        'class' => 'btn btn-primary approval_button',
                                                        'div'   => false
                                                    ]) ?>
                                            <?php } else {
                                                if ($goal['Collaborator']['approval_status'] === (string)Collaborator::STATUS_HOLD) { ?>
                                                    <?= $this->Form->button(__("Set as target of evaluation"),
                                                        [
                                                            'name'  => 'approval_btn',
                                                            'class' => 'btn btn-primary approval_button',
                                                            'div'   => false
                                                        ]) ?>
                                                <?php }
                                            } ?>
                                        <?php } ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <?= $this->Form->textarea('comment',
                                        [
                                            'label'                        => false,
                                            'class'                        => 'form-control addteam_input-design',
                                            'rows'                         => 3,
                                            'cols'                         => 30,
                                            'style'                        => 'margin-top: 10px; margin-bottom: 10px;',
                                            'placeholder'                  => 'コメントを書く',
                                            'data-bv-stringlength'         => 'true',
                                            'data-bv-stringlength-max'     => 5000,
                                            'data-bv-stringlength-message' => __(
                                                "It's over limit characters (%s).",
                                                5000),
                                            'data-bv-notempty-message'     => __(
                                                "Input is required."),
                                            'required'                     => 'required'
                                        ]) ?>
                                </div>

                                <div class="row">
                                    <div class="approval_botton_area">
                                        <?php if ($goal['my_goal'] === true) { ?>
                                            <?= $this->Form->button(__("Comment"),
                                                [
                                                    'name'  => 'comment_btn',
                                                    'class' => 'btn btn-primary approval_button',
                                                    'div'   => false
                                                ]) ?>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php } ?>

                            <?php if (isset($goal['ApprovalHistory']) === true && empty($goal['ApprovalHistory']) === false) { ?>
                                <?php foreach ($goal['ApprovalHistory'] as $history) { ?>
                                    <div class="font_12px comment-box" comment-id="">
                                        <div class="col col-xxs-12">
                                            <?= $this->Html->image('ajax-loader.gif', [
                                                'class'         => 'lazy comment-img',
                                                'data-original' => $this->Upload->uploadUrl($history['User'],
                                                    'User.photo',
                                                    ['style' => 'small'])
                                            ]) ?>
                                            <div class="comment-body">

                                                <div class="col col-xxs-12 comment-text comment-user">
                                                    <div
                                                        class="mb_2px lh_12px font_bold font_verydark"><?= h($history['User']['local_username']); ?></div>
                                                    <div
                                                        class="col col-xxs-12 showmore-comment comment-text feed-contents comment-contents font_verydark box-align"><?= h($history['comment']); ?></div>
                                                    <div
                                                        class="lh_15px"><?= $this->TimeEx->elapsedTime(h($history['created'])) ?></div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                            <?= $this->Form->end(); ?>
                        </div>

                    </div>
                <?php } ?>
            <?php } ?>
        </div>
    </div>
</div>
<?= $this->App->viewEndComment()?>
<?php $this->append('script') ?>
<script type="text/javascript">
    $(document).ready(function () {
        <?php foreach ($goal_info as $goal):?>
        <?php if(isset($goal['Collaborator']['id'])):?>
        $('#GoalApprovalIndexForm_<?= $goal['Collaborator']['id']?>').bootstrapValidator({
            live: 'enabled',

            fields: {}
        });
        <?php endif;?>
        <?php endforeach;?>
    });
</script>
<?php $this->end() ?>

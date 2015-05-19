<?
/**
 * @var CodeCompletionView $this
 * @var                    $unapproved_cnt
 * @var                    $done_cnt
 * @var                    $goal_info
 * @var                    $value_unit_list
 */
?>
<!-- START app/View/GoalApproval/index.ctp -->
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
        color: #ffffff;
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

<script type="text/javascript">
    var is_comment = function (str, button_no) {
        var button_name = 'modify_btn_' + button_no;
        if (str.length > 0) {
            document.getElementById(button_name).disabled = "";
        } else {
            document.getElementById(button_name).disabled = "disabled";
        }
    }
</script>

<div class="col col-md-12 sp-feed-alt-sub" style="top: 50px;" id="SubHeaderMenu">
    <div class="col col-xxs-6 text-align_r">
        <a class="font_lightGray-veryDark no-line plr_18px sp-feed-link inline-block pt_12px height_40px sp-feed-active"
           id="SubHeaderMenuFeed">
            <?= __d('gl', "処理待ち") ?>
            <? if ($unapproved_cnt > 0) { ?>
            <span class="btn btn-danger btn-xs approval_badge">
            <? echo $unapproved_cnt; ?>
            <? } ?>
            </span>
        </a>
    </div>
    <div class="col col-xxs-6">
        <a class="font_lightGray-veryDark no-line plr_18px sp-feed-link inline-block pt_12px height_40px"
           id="SubHeaderMenuGoal" href="<?= $this->Html->url(['controller' => 'goal_approval', 'action' => 'done']) ?>">
            <?= __d('gl', "処理済み") ?> <? if ($done_cnt > 0) {
                echo '(' . $done_cnt . ')';
            } ?></a>
    </div>
</div>

<div class="approval_body_start_area">
    <div class="row">
        <div class="col-sm-8 col-sm-offset-2">
            <? if (isset($goal_info) === true && count($goal_info) > 0) { ?>

                <? foreach ($goal_info as $key => $goal) { ?>

                    <div class="panel panel-default" id="AddGoalFormPurposeWrap">
                        <? if (isset($goal['status']) === true) { ?>
                            <div class="approval_status_box"><?= $goal['status']; ?></div>
                        <? } ?>

                        <div class="panel-body goal-set-heading clearfix">

                            <p class="approval_body_text">
                                <?= $this->Html->image('ajax-loader.gif', ['class'         => 'lazy comment-img',
                                                                           'data-original' => $this->Upload->uploadUrl($goal['User'],
                                                                                                                       'User.photo',
                                                                                                                       ['style' => 'small'])]) ?></p>

                            <p class="approval_body_text"><?= __d('gl', "名前") ?>
                                : <?= h($goal['User']['display_username']); ?></p>

                            <p class="approval_body_text"><?= __d('gl', "カテゴリ") ?>
                                : <?= h($goal['Goal']['GoalCategory']['name']); ?></p>

                            <p class="approval_body_text"><?= __d('gl', "ゴール名") ?>
                                : <?= h($goal['Goal']['name']); ?></p>

                            <p class="approval_body_text"><?= $goal['Collaborator']['type'] === '1' ?
                                    __d('gl', "リーダー") : __d('gl', "コラボレーター"); ?></p>

                            <p class="approval_body_text"><?= __d('gl', "役割") ?>
                                : <?= h($goal['Collaborator']['role']); ?></p>

                            <p class="approval_body_text"><?= __d('gl', "単位") ?>
                                : <?= $value_unit_list[$goal['Goal']['value_unit']]; ?></p>

                            <p class="approval_body_text"><?= __d('gl', "達成時") ?>
                                : <?= (double)$goal['Goal']['target_value']; ?></p>

                            <p class="approval_body_text"><?= __d('gl', "開始時") ?>
                                : <?= (double)$goal['Goal']['start_value']; ?></p>

                            <p class="approval_body_text"><?= __d('gl', "期限日") ?>
                                :                         <?= $this->TimeEx->date(h($goal['Goal']['end_date'])) ?></p>

                            <p class="approval_body_text"><?= __d('gl', "重要度") ?>
                                : <?= $goal['Collaborator']['priority']; ?></p>

                            <p class="approval_body_text"><?= __d('gl', "目的") ?>
                                : <?= h($goal['Goal']['Purpose']['name']); ?></p>

                            <p class="approval_body_text"><?= __d('gl', "詳細") ?>
                                : <?= $this->TextEx->autoLink($goal['Goal']['description']); ?></p>
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
                                                    ['url' => ['controller' => 'goal_approval', 'action' => 'index'], 'type' => 'post', 'novalidate' => true]); ?>
                            <?= $this->Form->hidden('collaborator_id', ['value' => $goal['Collaborator']['id']]); ?>

                            <div class="row">
                                <div class="approval_botton_area">
                                    <? if ($goal['my_goal'] === false) { ?>
                                        <?= $this->Form->button(__d('gl', "評価対象にする"),
                                                                ['name' => 'approval_btn', 'class' => 'btn btn-primary approval_button', 'div' => false]) ?>
                                        <?= $this->Form->button(__d('gl', "評価対象にしない"),
                                                                ['name' => 'wait_btn', 'class' => 'btn btn-Gray approval_button', 'div' => false]) ?>
                                    <? }
                                    elseif ($goal['my_goal'] === true && $goal['Collaborator']['type'] === '1' && $goal['Collaborator']['valued_flg'] === '3') { ?>
                                        <a class="btn btn-primary approval_button"
                                           href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'add', $goal['Goal']['id'], 'mode' => 3]) ?>"><?= __d('gl',
                                                                                                                                                                   "ゴールを修正する") ?>
                                            <i class="fa fa-chevron-right"></i></a>
                                    <? } ?>
                                </div>
                            </div>

                            <?= $this->Form->textarea('comment',
                                                      ['label' => false, 'onkeyup' => 'is_comment(value,' . $goal['Collaborator']['id'] . ')', 'class' => 'form-control addteam_input-design', 'rows' => 3, 'cols' => 30, 'style' => 'margin-top: 10px; margin-bottom: 10px;', 'placeholder' => 'コメントを書く']) ?>

                            <div class="row">
                                <div class="approval_botton_area">
                                    <? if ($goal['my_goal'] === true || ($goal['my_goal'] === false && $goal['Collaborator']['type'] === '0')) { ?>
                                        <?= $this->Form->button(__d('gl', "コメントする"),
                                                                ['name' => 'comment_btn', 'class' => 'btn btn-primary approval_button', 'div' => false]) ?>
                                    <? }
                                    else { ?>
                                        <?= $this->Form->button(__d('gl', "修正を依頼"),
                                                                ['id' => 'modify_btn_' . $goal['Collaborator']['id'], 'name' => 'modify_btn', 'class' => 'btn btn-Gray approval_button', 'div' => false, 'disabled']) ?>
                                    <? } ?>
                                </div>
                            </div>

                            <? if (isset($goal['ApprovalHistory']) === true && empty($goal['ApprovalHistory']) === false) { ?>
                                <? foreach ($goal['ApprovalHistory'] as $history) { ?>
                                    <div class="font_12px comment-box">
                                        <div class="col col-xxs-12">
                                            <?= $this->Html->image('ajax-loader.gif', ['class'         => 'lazy comment-img',
                                                                                       'data-original' => $this->Upload->uploadUrl($history['User'],
                                                                                                                                   'User.photo',
                                                                                                                                   ['style' => 'small'])]) ?>
                                            <div class="comment-body">

                                                <div class="col col-xxs-12 comment-text comment-user">
                                                    <div
                                                        class="mb_2px lh_12px font_bold font_verydark"><?= $history['User']['local_username']; ?></div>
                                                    <div
                                                        class="col col-xxs-12 showmore-comment comment-text feed-contents comment-contents font_verydark box-align"><?= $history['comment']; ?></div>
                                                    <div
                                                        class="lh_15px"><?= $this->TimeEx->elapsedTime(h($history['created'])) ?></div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                <? } ?>
                            <? } ?>
                            <?= $this->Form->end(); ?>
                        </div>
                    </div>
                <? } ?>
            <? } ?>
        </div>
    </div>
</div>
<!-- END app/View/GoalApproval/index.ctp -->

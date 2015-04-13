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
</style>

<div class="col col-md-12 sp-feed-alt-sub" style="top: 50px;" id="SubHeaderMenu">
    <div class="col col-xxs-6 text-align_r">
        <a class="font_lightGray-veryDark no-line plr_18px sp-feed-link inline-block pt_12px height_40px sp-feed-active"
           id="SubHeaderMenuFeed">
            <?= __d('gl', "処理待ち") ?> <? if ($unapproved_cnt > 0) {
                echo $unapproved_cnt;
            } ?></a>
    </div>
    <div class="col col-xxs-6">
        <a class="font_lightGray-veryDark no-line plr_18px sp-feed-link inline-block pt_12px height_40px"
           id="SubHeaderMenuGoal" href="<?= $this->Html->url(['controller' => 'goal_approval', 'action' => 'done']) ?>">
            <?= __d('gl', "処理済み") ?> <? if ($done_cnt > 0) {
                echo $done_cnt;
            } ?></a>
    </div>
</div>

<div class="approval_body_start_area">
    <div class="row">
        <div class="col-sm-8 col-sm-offset-2">
            <? if (isset($goal_info) === true && count($goal_info) > 0) { ?>
                <? foreach ($goal_info as $goal) { ?>
                    <div class="panel panel-default" id="AddGoalFormPurposeWrap">
                        <div class="panel-body goal-set-heading clearfix">

                            <? if (isset($goal['msg']) === true) { ?>
							<p class="approval_body_text">
							<p class="text-right"><?= $goal['msg']; ?></p>
							</p>
                            <? } ?>

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

                        <div class="panel-body ptb_8px plr_11px comment-block">
                            <textarea name="" class="form-control addteam_input-design disabled" placeholder="コメントを書く" rows="1" cols="30" style="margin-bottom: 8px;"></textarea>

                            <div class="row">
                                <div class="pull-right">
                                    <? if (isset($goal['msg']) === true) { ?>
                                        <a href="/goal_approval/comment/<?= $goal['Collaborator']['id']; ?>"
                                           class="btn btn-primary"><?= __d('gl', "コメントする") ?></a>
                                    <? } else { ?>
                                        <a href="/goal_approval/wait/<?= $goal['Collaborator']['id']; ?>"
                                           class="btn btn-link btn-lightGray bd-radius_4px"><?= __d('gl', "しない") ?></a>
                                        <a href="/goal_approval/approval/<?= $goal['Collaborator']['id']; ?>"
                                           class="btn btn-primary"><?= __d('gl', "評価対象とする") ?></a>
                                    <? } ?>
                                </div>
                            </div>

                            <? for ($i=0; $i<3; $i++) { ?>
                                <div class="font_12px comment-box" comment-id="">
                                    <div class="col col-xxs-12">
                                        <img src="" class="lazy comment-img" data-original="" alt="" style="display: block;">
                                        <div class="comment-body">

                                            <div class="col col-xxs-12 comment-text comment-user">
                                                <div class="mb_2px lh_12px font_bold font_verydark">投稿者名</div>
                                                <div class="col col-xxs-12 showmore-comment comment-text feed-contents comment-contents font_verydark box-align" id="">本文本文。。。。。。。。。。。。。</div>
                                                <div class="lh_15px"><span title="">YYYY/MM/DD HH:MM:SS</span></div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            <? } ?>
                        </div>

                    </div>
                <? } ?>
            <? } ?>
        </div>
    </div>
</div>
<!-- END app/View/GoalApproval/index.ctp -->

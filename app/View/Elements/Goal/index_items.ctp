<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 9/26/14
 * Time: 11:14 AM
 *
 * @var CodeCompletionView $this
 * @var                    $goals
 */
?>
<?= $this->App->viewStartComment() ?>
<?php foreach ($goals as $goal): ?>
    <div class="col col-xxs-12 my-goals-item">
        <div class="col col-xxs-3 col-xs-2">
            <a href="<?= $this->Html->url([
                'controller' => 'goals',
                'action'     => 'ajax_get_goal_description_modal',
                'goal_id'    => $goal['Goal']['id']
            ]) ?>"
               class="modal-ajax-get">
                <?=
                $this->Html->image('ajax-loader.gif',
                    [
                        'class'         => 'lazy img-rounded',
                        'style'         => 'width: 48px; height: 48px;',
                        'data-original' => $this->Upload->uploadUrl($goal, 'Goal.photo',
                            ['style' => 'medium'])
                    ]
                )
                ?></a>
        </div>
        <div class="col col-xxs-9 col-xs-10 pl_5px">
            <div class="col col-xxs-12 goals-page-card-title-wrapper">
                <a href="<?= $this->Html->url([
                    'controller' => 'goals',
                    'action'     => 'ajax_get_goal_description_modal',
                    'goal_id'    => $goal['Goal']['id']
                ]) ?>"
                   class="modal-ajax-get goals-page-card-title">
                    <p class="font_verydark goals-page-card-title-text">
                        <span><?= h($goal['Goal']['name']) ?></span>
                    </p>
                </a>
            </div>
            <div class="col col-xxs-12 font_lightgray font_12px">
                <?php if (!empty($goal['Leader'])): ?>
                    <?=
                    __("Leader: %s",
                        h($goal['Leader'][0]['User']['display_username'])) ?>
                <?php endif; ?>
            </div>
            <div class="col col-xxs-12 font_lightgray font_12px">
                <i class="fa fa-check-circle"></i><?= "&nbsp;" . count(($goal['ActionResult'])) . "&nbsp;･&nbsp;" ?>
                <i class="fa fa-key"></i><?= "&nbsp;" . count($goal['KeyResult']) . "&nbsp;･" ?>
                <i class="fa fa-heart"></i><?= "&nbsp;" . count($goal['Follower']) . "&nbsp;･" ?>
                <i class="fa fa-child"></i><?= "&nbsp;" . count($goal['GoalMember']) . "&nbsp;" ?>
                <?= $this->Goal->displayGoalMemberNameList($goal['GoalMember']) ?>
            </div>
            <?php if ($goal['Goal']['user_id'] != $this->Session->read('Auth.User.id') && isset($goal['Goal']) && is_null($goal['Goal']['completed'])): ?>
                <div class="col col-xxs-12 mt_5px">
                    <? $follow_opt = $this->Goal->getFollowOption($goal) ?>
                    <? $collabo_opt = $this->Goal->getCollaboOption($goal) ?>
                    <div class="col col-xxs-6 col-xs-4 mr_5px">
                        <a class="btn btn-white font_verydark bd-circle_22px toggle-follow p_8px <?= $follow_opt['class'] ?>"
                           href="#"
                           data-class="toggle-follow"
                           goal-id="<?= $goal['Goal']['id'] ?>"
                            <?php if ($follow_opt['disabled'] || $this->Goal->isCoachingUserGoal($goal,
                                    viaIsSet($my_coaching_users))
                            ): ?>
                                disabled="disabled"
                            <?php endif ?>
                        >
                            <i class="fa fa-heart font_rougeOrange" style="<?= h($follow_opt['style']) ?>"></i>
                            <span class="ml_5px"><?= h($follow_opt['text']) ?></span>
                        </a>
                    </div>
                    <div class="col col-xxs-5 col-xs-4">
                        <a class="btn btn-white bd-circle_22px font_verydark modal-ajax-get-collabo p_8px <?= h($collabo_opt['class']) ?>"
                           data-toggle="modal"
                           data-target="#ModalCollabo_<?= $goal['Goal']['id'] ?>"
                           href="<?= $this->Html->url([
                               'controller' => 'goals',
                               'action'     => 'ajax_get_collabo_change_modal',
                               'goal_id'    => $goal['Goal']['id']
                           ]) ?>">
                            <i class="fa fa-child font_rougeOrange font_18px"
                               style="<?= h($collabo_opt['style']) ?>"></i>
                            <span class="ml_5px font_14px"><?= h($collabo_opt['text']) ?></span>
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endforeach ?>
<?= $this->App->viewEndComment() ?>

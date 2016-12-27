<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 9/26/14
 * Time: 11:14 AM
 *
 * @var CodeCompletionView $this
 * @var                    $goals
 * @var                    $type
 * @var                    $current_term
 */
$kr_count = 0;
?>
<?= $this->App->viewStartComment() ?>
<?php foreach ($goals as $goal): ?>
    <?php
    if (isset($goal['Goal']['id']) && !empty($goal['Goal']['id'])) {
        //Changing the height of the vertical lines by the number of KR | 縦線の高さをKRの数によって変化させる
        $kr_line_height = 40;
        $kr_incomp_count = count($goal['IncompleteKeyResult']);
        $kr_comp_count = count($goal['CompleteKeyResult']);
        $kr_count = $kr_comp_count + $kr_incomp_count;
        $kr_prog_percent = 0;
        if ($kr_comp_count != 0 && $kr_count != 0) {
            $kr_prog_percent = round($kr_comp_count / $kr_count, 2) * 100;
        }
        if ($kr_count > 0) {
            if ($kr_count > MY_GOAL_AREA_FIRST_VIEW_KR_COUNT) {
                $kr_line_height += 64 * (MY_GOAL_AREA_FIRST_VIEW_KR_COUNT + 1);
            } else {
                $kr_line_height += 64 * $kr_count;
            }
        } else {
            $kr_line_height = 30;
        }
    }

    ?>
    <div class="dashboard-goals-card">
        <!-- Class is changed, whether goal name is set or not | goal名のあるなしでclassを切り替える -->
        <!-- If there is no goal name, then there is no vertical-border | goal名がない場合はタテ線を出さない -->
        <div class="
            <?php if (isset($goal['Goal']['id']) && !empty($goal['Goal']['id'])): ?>
                dashboard-goals-card-header">
            <div class="dashboard-goals-card-vertical-line" style="height: <?= $kr_line_height ?>px;"
                 id="KRsVerticalLine_<?= $goal['Goal']['id'] ?>"></div>
            <?php else: ?>
                dashboard-goals-card-header-noname">
            <?php endif; ?>
            <i class="dashboard-goals-card-header-icon fa fa-flag-o jsGoalsCardProgress"
               goal-prog-percent="<?= isset($kr_prog_percent) ? $kr_prog_percent : 0; ?>">
            </i>

            <div class="dashboard-goals-card-header-title">
                <?php if (empty($goal['Goal'])): ?>
                    <a href="<?= $this->Html->url([
                        'controller' => 'goals',
                        'action'     => 'add',
                        'mode'       => 2
                    ]) ?>"
                       class="dashboard-goals-card-header-goal-set">
                        <i class="fa fa-plus-circle dashboard-goals-card-header-goal-set-icon"></i>
                        <?= __('Add Reference Values') ?>
                    </a>
                <?php else: ?>
                    <div class="dashboard-goals-card-header-goal-wrap">
                        <a href="<?= $this->Html->url([
                            'controller' => 'goals',
                            'action'     => 'view_info',
                            'goal_id'    => $goal['Goal']['id']
                        ]) ?>"
                           class="">
                            <p class="dashboard-goals-card-header-goal">
                                <?= h($goal['Goal']['name']) ?>
                            </p>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            <?php if ($type == 'leader'): ?>
                <a class="dashboard-goals-card-header-function dropdown"
                   data-toggle="dropdown"
                   id="download">
                    <i class="fa fa-cog goals-column-function-icon"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-right frame-arrow-icon-goal" role="menu"
                    aria-labelledby="dropdownMenu1">
                    <?php //目的のみの場合とそうでない場合でurlが違う
                    $del_url = [
                        'controller' => 'goals',
                        'action'     => 'delete_purpose',
                    ];
                    if (isset($goal['Goal']['id']) && !empty($goal['Goal']['id'])) {
                        $del_url = ['controller' => 'goals', 'action' => 'delete', 'goal_id' => $goal['Goal']['id']];
                    }
                    ?>
                    <?php if (!empty($goal['Goal'])): ?>
                        <li role="presentation">
                            <a href="<?= $this->Html->url([
                                'controller' => 'goals',
                                'action'     => 'ajax_get_add_key_result_modal',
                                'goal_id'    => $goal['Goal']['id']
                            ]) ?>"
                               class="modal-ajax-get-add-key-result">
                                <i class="fa fa-plus-circle"></i><span class="ml_2px">
                                        <?= __("Add Key Result") ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (!Hash::get($goal, 'Evaluation')): ?>
                        <li role="presentation"><a role="menuitem" tabindex="-1"
                                                   href="/goals/<?= $goal['Goal']['id'] ?>/edit">
                                <i class="fa fa-pencil"></i><span class="ml_2px"><?= __("Edit goal") ?></span>
                            </a>
                        </li>
                        <?php if (count($goal['KeyResult']) > 1 && !$isStartedEvaluation):?>
                            <li role="presentation">
                                <a role="menuitem" tabindex="-1"
                                   class="modal-ajax-get-exchange-tkr"
                                   href="<?= $this->Html->url([
                                       'controller' => 'goals',
                                       'action'     => 'ajax_get_exchange_tkr_modal',
                                       'goal_id'    => $goal['Goal']['id']
                                   ]) ?>">
                                    <hr class="dashboard-goals-card-horizontal-line">
                                    <i class="fa fa-exchange"></i>
                                    <span class="ml_2px"><?= __("Change TKR") ?></span>
                                </a>
                            </li>
                        <?php endif;?>
                        <!-- リーダー変更 -->
                        <?php if ($goal['Goal']['can_change_leader']):?>
                            <li role="presentation">
                                <a role="menuitem" tabindex="-1"
                                   class="modal-ajax-get-exchange-leader"
                                   href="<?= $this->Html->url([
                                       'controller' => 'goals',
                                       'action'     => 'ajax_get_exchange_leader_modal',
                                       'goal_id'    => $goal['Goal']['id']
                                   ]) ?>">
                                    <hr class="dashboard-goals-card-horizontal-line">
                                    <i class="fa fa-exchange"></i>
                                    <span class="ml_2px"><?= __("Change leader") ?></span>
                                </a>
                            </li>
                        <?php endif;?>
                        <li role="presentation">
                            <?=
                            $this->Form->postLink('<i class="fa fa-trash"></i><span class="ml_5px">' .
                                __("Delete goal") . '</span>',
                                $del_url,
                                ['escape' => false], __("Do you really want to delete this goal?")) ?>
                        </li>
                    <?php endif; ?>
                    <?php if (in_array($goal['Goal']['id'], $canCompleteGoalIds)):?>
                        <li role="presentation">
                            <?=
                            $this->Form->postLink('<i class="fa fa-hand-stop-o"></i><span class="ml_5px">' .
                                __("Achieve goal") . '</span>',
                                "/goals/complete/".$goal['Goal']['id'],
                                ['escape' => false], __("Do you really want to complete this goal?")) ?>
                        </li>
                    <?php endif;?>
                </ul>
            <?php elseif
            ($type == 'collabo'
            ): ?>
                <a href="#"
                   class="dashboard-goals-card-header-function dropdown"
                   data-toggle="dropdown"
                   id="download">
                    <i class="fa fa-cog goals-column-function-icon"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-right frame-arrow-icon-collabo" role="menu"
                    aria-labelledby="dropdownMenu1">
                    <?php if (isset($goal['Goal']['id']) && !empty($goal['Goal']['id'])): ?>
                        <li role="presentation">
                            <a href="<?= $this->Html->url([
                                'controller' => 'goals',
                                'action'     => 'ajax_get_add_key_result_modal',
                                'goal_id'    => $goal['Goal']['id']
                            ]) ?>"
                               class="modal-ajax-get-add-key-result"
                            ><i class="fa fa-plus-circle"></i><span class="ml_2px">
                                    <?= __("Add Key Result") ?></span></a>
                            <a class="modal-ajax-get-collabo collaborate-button"
                               data-toggle="modal"
                               data-target="#ModalCollabo_<?= $goal['Goal']['id'] ?>"
                               href="<?= $this->Html->url([
                                   'controller' => 'goals',
                                   'action'     => 'ajax_get_collabo_change_modal',
                                   'goal_id'    => $goal['Goal']['id']
                               ]) ?>">
                                <i class="fa fa-pencil"></i>
                                <span class="ml_2px"><?= __("Edit Collabo") ?></span>
                            </a>
                        </li>
                        <!-- リーダー変更 -->
                        <?php if ($goal['Goal']['can_change_leader']):?>
                            <li role="presentation">
                                <a role="menuitem" tabindex="-1"
                                   class="modal-ajax-get-exchange-leader"
                                   href="<?= $this->Html->url([
                                       'controller' => 'goals',
                                       'action'     => 'ajax_get_exchange_leader_modal',
                                       'goal_id'    => $goal['Goal']['id']
                                   ]) ?>">
                                    <hr class="dashboard-goals-card-horizontal-line">
                                    <i class="fa fa-exchange"></i>
                                    <span class="ml_2px"><?= __("Change leader") ?></span>
                                </a>
                            </li>
                        <?php endif;?>
                    <?php endif; ?>
                </ul>
            <?php elseif
            ($type == 'my_prev'
            ): ?>
                <div class="pull-right goals-column-function bd-radius_4px dropdown">
                    <a href="#" class="dashboard-goals-card-header-function dropdown"
                       data-toggle="dropdown"
                       id="download">
                        <i class="fa fa-cog"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right frame-arrow-icon" role="menu"
                        aria-labelledby="dropdownMenu1">
                        <?php //目的のみの場合とそうでない場合でurlが違う
                        $del_url = [
                            'controll er' => 'goals',
                            'action'      => 'delete_purpose',
                        ];
                        if (isset($goal['Goal']['id']) && !empty($goal['Goal']['id'])) {
                            $del_url = ['controller' => 'goals', 'action' => 'delete', $goal['Goal']['id']];
                        }
                        ?>
                        <?php if (!empty($goal['Goal'])): ?>
                            <li role="presentation">
                                <a href="<?= $this->Html->url([
                                    'controller' => 'goals',
                                    'action'     => 'ajax_get_add_key_result_modal',
                                    'goal_id'    => $goal['Goal']['id']
                                ]) ?>"
                                   class="modal-ajax-get-add-key-result">
                                    <i class="fa fa-plus-circle"></i><span class="ml_2px">
                                            <?= __("Add Key Result") ?></span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
        <?php if (isset($goal['Goal']['id']) && !empty($goal['Goal']['id'])): ?>
            <div class="dashboard-goals-card-body shadow-default">
                <?php if (isset($goal['Goal']['id'])): ?>
                    <div class="goalsCard-krSeek">

                        <ul class="dashboard-goals-card-body-krs-wrap">
                            <?= $this->element('Goal/key_result_items',
                                [
                                    'key_results'         => $goal['KeyResult'],
                                    'is_init'             => true,
                                    'kr_can_edit'         => true,
                                    'goal_id'             => $goal['Goal']['id'],
                                    'can_add_action'      => $goal['Goal']['end_date'] >= $current_term['start_date'] && $goal['Goal']['end_date'] <= $current_term['end_date'] ? true : false,
                                    'incomplete_kr_count' => count($goal['IncompleteKeyResult'])
                                ]); ?>
                            <?php if ($kr_count > MY_GOAL_AREA_FIRST_VIEW_KR_COUNT): ?>
                                <li class="dashboard-goals-card-body-krs-ellipsis"
                                    id="KrRemainOpenWrap_<?= $goal['Goal']['id'] ?>">
                                    <a href="#" target-id="KrRemainOpenWrap_<?= $goal['Goal']['id'] ?>"
                                       ajax-url="<?= $this->Html->url([
                                           'controller'    => 'goals',
                                           'action'        => 'ajax_get_key_results',
                                           'goal_id'       => $goal['Goal']['id'],
                                           'extract_count' => MY_GOAL_AREA_FIRST_VIEW_KR_COUNT,
                                           true
                                       ]) ?>"
                                       id="KRsOpen_<?= $goal['Goal']['id'] ?>"
                                       kr-line-id="KRsVerticalLine_<?= $goal['Goal']['id'] ?>"
                                       class="replace-ajax-get-kr-list dashboard-goals-card-body-krs-ellipsis-link">
                                        <i class="fa fa-ellipsis-v dashboard-goals-card-krs-ellipsis-icon"></i>

                                        <p class="dashboard-goals-card-body-krs-ellipsis-number"><?= $kr_count - MY_GOAL_AREA_FIRST_VIEW_KR_COUNT ?>
                                            +</p>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <li class="dashboard-goals-card-body-add-kr clearfix">
                                <a class="dashboard-goals-card-body-add-kr-link modal-ajax-get-add-key-result"
                                   href="<?= $this->Html->url([
                                       'controller' => 'goals',
                                       'action'     => 'ajax_get_add_key_result_modal',
                                       'goal_id'    => $goal['Goal']['id']
                                   ]) ?>">
                                    <hr class="dashboard-goals-card-horizontal-line">
                                    <i class="fa fa-plus dashboard-goals-card-body-add-kr-icon"></i>

                                    <p class="dashboard-goals-card-body-add-kr-contents"><?= __("Add Key Result") ?></p>
                                </a>

                                <p class="dashboard-goals-card-body-goal-status"><?= $this->Goal->displayApprovalStatus($goal['TargetCollabo']) ?></p>
                            </li>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <!-- only purpose, don't display krs | ゴール定めていないものはKRエリアに何も表示しない -->
        <?php endif; ?>
    </div>
<?php endforeach ?>
<?= $this->App->viewEndComment() ?>

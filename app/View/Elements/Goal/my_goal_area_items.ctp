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
<?= $this->App->viewStartComment()?>
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
                        'purpose_id' => $goal['Purpose']['id'],
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
                <div class="dashboard-goals-card-header-purpose">
                    <?= h($goal['Purpose']['name']) ?>
                </div>
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
                    $edit_url = [
                        'controller' => 'goals',
                        'action'     => 'add',
                        'mode'       => 2,
                        'purpose_id' => $goal['Purpose']['id']
                    ];
                    $del_url = [
                        'controller' => 'goals',
                        'action'     => 'delete_purpose',
                        'purpose_id' => $goal['Purpose']['id']
                    ];
                    if (isset($goal['Goal']['id']) && !empty($goal['Goal']['id'])) {
                        $edit_url = [
                            'controller' => 'goals',
                            'action'     => 'add',
                            'goal_id'    => $goal['Goal']['id'],
                            'mode'       => 3
                        ];
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
                    <?php if (!viaIsSet($goal['Evaluation'])): ?>
                        <li role="presentation"><a role="menuitem" tabindex="-1"
                                                   href="<?= $this->Html->url($edit_url) ?>">
                                <i class="fa fa-pencil"></i><span class="ml_2px"><?= __("Edit goal") ?></span>
                            </a>
                        </li>
                        <li role="presentation">
                            <?=
                            $this->Form->postLink('<i class="fa fa-trash"></i><span class="ml_5px">' .
                                __("Delete goal") . '</span>',
                                $del_url,
                                ['escape' => false], __("Do you really want to delete this goal?")) ?>
                        </li>
                    <?php endif; ?>
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
                            <a class="modal-ajax-get-collabo"
                               data-toggle="modal"
                               data-target="#ModalCollabo_<?= $goal['Goal']['id'] ?>"
                               href="<?= $this->Html->url([
                                   'controller' => 'goals',
                                   'action'     => 'ajax_get_collabo_change_modal',
                                   'goal_id'    => $goal['Goal']['id']
                               ]) ?>">
                                <i class="fa fa-pencil"></i>
                                <span class="ml_2px"><?= __("Edit Collaborate") ?></span>
                            </a>
                        </li>
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
                        $edit_url = [
                            'controller' => 'goals',
                            'action'     => 'add',
                            'mode'       => 2,
                            'purpose_id' => $goal['Purpose']['id']
                        ];
                        $del_url = [
                            'controll er' => 'goals',
                            'action'      => 'delete_purpose',
                            'purpose_id'  => $goal['Purpose']['id']
                        ];
                        if (isset($goal['Goal']['id']) && !empty($goal['Goal']['id'])) {
                            $edit_url = [
                                'controller' => 'goals',
                                'action'     => 'add',
                                'goal_id'    => $goal['Goal']['id'],
                                'mode'       => 3
                            ];
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
                        <?php if (isset($goal['Goal']['end_date']) && !empty($goal['Goal']['end_date'])): ?>

                            <!-- 認定待ちと残り日数 -->
                            <!-- <div class="pull-right font_12px">
                                <?php if (($limit_day = ($goal['Goal']['end_date'] - REQUEST_TIMESTAMP) / (60 * 60 * 24)) < 0): ?>
                                    <?= __("%d days pass", $limit_day * -1) ?>
                                <?php else: ?>
                                    <?php if (isset($goal['Goal']['owner_approval_flag']) === true) : ?>
                                        <?php if ($goal['Goal']['owner_approval_flag'] === '0') : ?>
                                            <span style="color:red"><?= __("Waiting for approval") ?></span>
                                        <?php elseif ($goal['Goal']['owner_approval_flag'] === '1') : ?>
                                            <span style="color:#00BFFF"><?= __("In Evaluation") ?></span>
                                        <?php elseif ($goal['Goal']['owner_approval_flag'] === '2') : ?>
                                            <?= __("Out of Evaluation") ?>
                                        <?php elseif ($goal['Goal']['owner_approval_flag'] === '3') : ?>
                                            <span style="color:red"><?= __("Waiting for modified") ?></span>
                                        <?php endif ?>
                                        ・
                                    <?php endif; ?>
                                    <?= __("%d days left", $limit_day) ?>
                                <?php endif; ?>
                            </div> -->
                        <?php endif; ?>


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
                            <? endif; ?>
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

                                <p class="dashboard-goals-card-body-goal-status"><?= Collaborator::$STATUS[$goal['MyCollabo'][0]['valued_flg']] ?></p>
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
<?= $this->App->viewEndComment()?>

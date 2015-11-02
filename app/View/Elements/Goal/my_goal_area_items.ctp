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
 */
?>
<!-- START app/View/Elements/Goal/my_goal_area_items.ctp -->
<?php foreach ($goals as $goal): ?>
    <div class="dashboard-goals-card">
        <!-- Class is changed, whether goal name is set or not | goal名のあるなしでclassを切り替える -->
        <!-- If there is no goal name, then there is no vertical-border | goal名がない場合はタテ線を出さない -->
        <div class="
            <?php if (isset($goal['Goal']['id']) && !empty($goal['Goal']['id'])): ?>
                dashboard-goals-card-header">
            <?php
            //Changing the height of the vertical lines by the number of KR | 縦線の高さをKRの数によって変化させる
            $kr_line_height = 40;
            $kr_count = count($goal['KeyResult']);
            if ($kr_count > 0) {
                if ($kr_count >= 3) {
                    $kr_line_height = 232;
                }
                else {
                    $kr_line_height += 64 * $kr_count;
                }
            }else{
                $kr_line_height = 30;
            }
            ?>
            <div class="dashboard-goals-card-vertical-line" style="height: <?= $kr_line_height ?>px;"
                 id="KRsVerticalLine_<?= $goal['Goal']['id'] ?>"></div>
            <?php else: ?>
                dashboard-goals-card-header-noname">
            <?php endif; ?>
            <i class="dashboard-goals-card-header-icon fa fa-flag-o jsGoalsCardProgress"
               goalProgPercent="<?= isset($goal['Goal']['progress']) ? $goal['Goal']['progress'] : 0 ?>">
            </i>

            <div class="dashboard-goals-card-header-title">
                <?php if (empty($goal['Goal'])): ?>
                    <a href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'add', 'purpose_id' => $goal['Purpose']['id'], 'mode' => 2]) ?>"
                       class="dashboard-goals-card-header-goal-set">
                        <i class="fa fa-plus-circle dashboard-goals-card-header-goal-set-icon"></i><?= __d('gl',
                                                                                                           '基準を追加する') ?>
                    </a>
                <?php else: ?>
                    <div class="dashboard-goals-card-header-goal-wrap">
                        <a href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'view_info', 'goal_id' => $goal['Goal']['id']]) ?>"
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
                <ul class="dropdown-menu dropdown-menu-right frame-arrow-icon" role="menu"
                    aria-labelledby="dropdownMenu1">
                    <?php //目的のみの場合とそうでない場合でurlが違う
                    $edit_url = ['controller' => 'goals', 'action' => 'add', 'mode' => 2, 'purpose_id' => $goal['Purpose']['id']];
                    $del_url = ['controller' => 'goals', 'action' => 'delete_purpose', 'purpose_id' => $goal['Purpose']['id']];
                    if (isset($goal['Goal']['id']) && !empty($goal['Goal']['id'])) {
                        $edit_url = ['controller' => 'goals', 'action' => 'add', 'goal_id' => $goal['Goal']['id'], 'mode' => 3];
                        $del_url = ['controller' => 'goals', 'action' => 'delete', 'goal_id' => $goal['Goal']['id']];
                    }
                    ?>
                    <?php if (!empty($goal['Goal'])): ?>
                        <li role="presentation">
                            <a href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_add_key_result_modal', 'goal_id' => $goal['Goal']['id']]) ?>"
                               class="modal-ajax-get-add-key-result">
                                <i class="fa fa-plus-circle"></i><span class="ml_2px">
                                        <?= __d('gl', "達成要素を追加") ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (!viaIsSet($goal['Evaluation'])): ?>
                        <li role="presentation"><a role="menuitem" tabindex="-1"
                                                   href="<?= $this->Html->url($edit_url) ?>">
                                <i class="fa fa-pencil"></i><span class="ml_2px"><?= __d('gl', "ゴールを編集") ?></span>
                            </a>
                        </li>
                        <li role="presentation">
                            <?=
                            $this->Form->postLink('<i class="fa fa-trash"></i><span class="ml_5px">' .
                                                  __d('gl', "ゴールを削除") . '</span>',
                                                  $del_url,
                                                  ['escape' => false], __d('gl', "本当にこのゴールを削除しますか？")) ?>
                        </li>
                    <?php endif; ?>
                </ul>
            <?php elseif
            ($type == 'collabo'
            ): ?>
                <a href="#"
                   class="goals-column-function pull-right goals-column-function bd-radius_4px dropdown font_lightGray-gray"
                   data-toggle="dropdown"
                   id="download">
                    <i class="fa fa-cog goals-column-function-icon"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-right frame-arrow-icon" role="menu"
                    aria-labelledby="dropdownMenu1">
                    <?php if (isset($goal['Goal']['id']) && !empty($goal['Goal']['id'])): ?>
                        <li role="presentation">
                            <a href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_add_key_result_modal', 'goal_id' => $goal['Goal']['id']]) ?>"
                               class="modal-ajax-get-add-key-result"
                                ><i class="fa fa-plus-circle"></i><span class="ml_2px">
                                    <?= __d('gl', "達成要素を追加") ?></span></a>
                            <a class="modal-ajax-get-collabo"
                               data-toggle="modal"
                               data-target="#ModalCollabo_<?= $goal['Goal']['id'] ?>"
                               href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_collabo_change_modal', 'goal_id' => $goal['Goal']['id']]) ?>">
                                <i class="fa fa-pencil"></i>
                                <span class="ml_2px"><?= __d('gl', "コラボを編集") ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            <?php elseif
            ($type == 'my_prev'
            ): ?>
                <div class="pull-right goals-column-function bd-radius_4px dropdown">
                    <a href="#" class="font_lightGray-gray font_14px plr_4px pt_1px pb_2px"
                       data-toggle="dropdown"
                       id="download">
                        <i class="fa fa-cog"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right frame-arrow-icon" role="menu"
                        aria-labelledby="dropdownMenu1">
                        <?php //目的のみの場合とそうでない場合でurlが違う
                        $edit_url = ['controller' => 'goals', 'action' => 'add', 'mode' => 2, 'purpose_id' => $goal['Purpose']['id']];
                        $del_url = ['controll er' => 'goals', 'action' => 'delete_purpose', 'purpose_id' => $goal['Purpose']['id']];
                        if (isset($goal['Goal']['id']) && !empty($goal['Goal']['id'])) {
                            $edit_url = ['controller' => 'goals', 'action' => 'add', 'goal_id' => $goal['Goal']['id'], 'mode' => 3];
                            $del_url = ['controller' => 'goals', 'action' => 'delete', $goal['Goal']['id']];
                        }
                        ?>
                        <?php if (!empty($goal['Goal'])): ?>
                            <li role="presentation">
                                <a href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_add_key_result_modal', 'goal_id' => $goal['Goal']['id']]) ?>"
                                   class="modal-ajax-get-add-key-result">
                                    <i class="fa fa-plus-circle"></i><span class="ml_2px">
                                            <?= __d('gl', "達成要素を追加") ?></span>
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
                                    <?= __d('gl', "%d日経過", $limit_day * -1) ?>
                                <?php else: ?>
                                    <?php if (isset($goal['Goal']['owner_approval_flag']) === true) : ?>
                                        <?php if ($goal['Goal']['owner_approval_flag'] === '0') : ?>
                                            <span style="color:red"><?= __d('gl', "認定待ち") ?></span>
                                        <?php elseif ($goal['Goal']['owner_approval_flag'] === '1') : ?>
                                            <span style="color:#00BFFF"><?= __d('gl', "評価対象") ?></span>
                                        <?php elseif ($goal['Goal']['owner_approval_flag'] === '2') : ?>
                                            <?= __d('gl', "評価対象外") ?>
                                        <?php elseif ($goal['Goal']['owner_approval_flag'] === '3') : ?>
                                            <span style="color:red"><?= __d('gl', "修正待ち") ?></span>
                                        <?php endif ?>
                                        ・
                                    <?php endif; ?>
                                    <?= __d('gl', "残り%d日", $limit_day) ?>
                                <?php endif; ?>
                            </div> -->
                        <?php endif; ?>


                        <ul class="dashboard-goals-card-body-krs-wrap">
                            <?= $this->element('Goal/key_result_items',
                                               ['key_results'         => $goal['KeyResult'],
                                                'is_init'             => true, 'kr_can_edit' => true,
                                                'goal_id'             => $goal['Goal']['id'],
                                                'incomplete_kr_count' => count($goal['IncompleteKeyResult'])
                                               ]); ?>
                            <?php if (count($goal['KeyResult']) > 2): ?>
                                <li class="dashboard-goals-card-body-krs-ellipsis"
                                    id="KrRemainOpenWrap_<?= $goal['Goal']['id'] ?>">
                                    <a href="#" target-id="KrRemainOpenWrap_<?= $goal['Goal']['id'] ?>"
                                       ajax-url="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_key_results', 'goal_id' => $goal['Goal']['id'], 'extract_count' => 2, true]) ?>"
                                       id="KRsOpen_<?= $goal['Goal']['id'] ?>"
                                       kr-line-id="KRsVerticalLine_<?= $goal['Goal']['id'] ?>"
                                       class="replace-ajax-get-kr-list dashboard-goals-card-body-krs-ellipsis-link">
                                        <i class="fa fa-ellipsis-v dashboard-goals-card-krs-ellipsis-icon"></i>

                                        <p class="dashboard-goals-card-body-krs-ellipsis-number"><?= count($goal['IncompleteKeyResult']) + count($goal['CompleteKeyResult']) - 2 ?>
                                            +</p>
                                    </a>
                                </li>
                            <? endif; ?>
                            <li class="dashboard-goals-card-body-add-kr clearfix">
                                <a class="dashboard-goals-card-body-add-kr-link modal-ajax-get-add-key-result"
                                   href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_add_key_result_modal', 'goal_id' => $goal['Goal']['id']]) ?>">
                                    <hr class="dashboard-goals-card-horizontal-line">
                                    <i class="fa fa-plus dashboard-goals-card-body-add-kr-icon"></i>

                                    <p class="dashboard-goals-card-body-add-kr-contents"><?= __d('gl', "達成要素を追加") ?></p>
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
<!-- End app/View/Elements/Goal/my_goal_area_items.ctp -->

<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 7/9/15
 * Time: 3:33 PM
 *
 * @var CodeCompletionView $this
 * @var                    $goals
 * @var                    $display_action_count
 * @var                    $is_mine
 * @var                    $page_type
 * @var                    $my_goals_count
 * @var                    $follow_goals_count
 */
?>
<?= $this->App->viewStartComment() ?>
<div class="col-sm-8 col-sm-offset-2">
    <div class="panel panel-default">
        <?= $this->element('User/simplex_top_section') ?>
        <div class="panel-body view-goals-panel">
            <div class="input-group profile-user-goals-terms">
                    <span class="input-group-addon profile-user-goals-terms-icon-wrap" id="">
                        <i class="profile-user-goals-terms-icon fa fa-calendar-o"></i>
                    </span>
                <?=
                $this->Form->input('term_id', [
                    'label'        => false,
                    'div'          => false,
                    'required'     => true,
                    'class'        => 'form-control disable-change-warning profile-user-goals-terms-select',
                    'id'           => 'LoadTermGoal',
                    'options'      => $term,
                    'default'      => $term_id,
                    'redirect-url' => $term_base_url,
                    'wrapInput'    => 'profile-user-goals-terms-select-wrap'
                ])
                ?>
            </div>
            <br>

            <div class="profile-goals-select-wrap btn-group mb_12px" role="group">
                <a href="<?= $this->Html->url([
                    'controller' => 'users',
                    'action'     => 'view_goals',
                    'user_id'    => $user['User']['id'],
                    'term_id'    => $term_id
                ]) ?>"
                   class="profile-goals-select btn <?= $page_type == "following" ? "btn-unselected" : "btn-selected" ?>">
                    <?= __("My Goal (%s)", $my_goals_count) ?></a>
                <a href="<?= $this->Html->url([
                    'controller' => 'users',
                    'action'     => 'view_goals',
                    'user_id'    => $user['User']['id'],
                    'term_id'    => $term_id,
                    'page_type'  => 'following'
                ]) ?>"
                   class="profile-goals-select btn <?= $page_type == "following" ? "btn-selected" : "btn-unselected" ?>">
                    <?= __("Following (%s)", $follow_goals_count) ?></a>
            </div>
            <?php foreach ($goals as $goal): ?>
                <div class="col col-xxs-12 my-goals-item">
                    <div class="col col-xxs-2 col-xs-2">
                        <a href="<?= $this->Html->url([
                            'controller' => 'goals',
                            'action'     => 'view_krs',
                            'goal_id'    => $goal['Goal']['id']
                        ]) ?>">
                            <?=
                            $this->Html->image('pre-load.svg',
                                [
                                    'class'         => 'lazy img-rounded profile-goals-img',
                                    'data-original' => $this->Upload->uploadUrl($goal, 'Goal.photo',
                                        ['style' => 'x_large'])
                                ]
                            )
                            ?></a>
                    </div>
                    <div class="col col-xxs-10 col-xs-10 pl_5px">
                        <div class="col col-md-11 col-xs-10 col-xxs-9 profile-goals-card-title-wrapper">
                            <a href="<?= $this->Html->url([
                                'controller' => 'goals',
                                'action'     => 'view_krs',
                                'goal_id'    => $goal['Goal']['id']
                            ]) ?>"
                               class="profile-goals-card-title">
                                <p class="font_verydark profile-goals-card-title-text">
                                    <span><?= h($goal['Goal']['name']) ?></span>
                                </p>
                            </a>
                        </div>
                        <?php if ($is_mine && $page_type != "following"): ?>
                            <?= $this->element('Goal/goal_menu_on_my_page', compact('goal')) ?>
                        <?php endif; ?>
                        <div class="col col-xxs-12 font_lightgray font_12px">
                            <?php if ($page_type !== 'following'): ?>
                                <?= __("Approval Status: %s",
                                    $this->Goal->displayApprovalStatus($goal['TargetCollabo'])) ?>
                            <?php endif; ?>
                        </div>
                        <div class="col col-xxs-12">
                            <div class="progress mb_0px goals-column-progress-bar">
                                <span class="progress-text"><?= h($goal['Goal']['progress']) ?>%</span>
                                <div class="progress-bar progress-bar-info" role="progressbar"
                                     aria-valuenow="<?= h($goal['Goal']['progress']) ?>" aria-valuemin="0"
                                     aria-valuemax="100" style="width: <?= h($goal['Goal']['progress']) ?>%;">
                                </div>
                            </div>
                        </div>

                        <?php if ($page_type != "following"): ?>
                            <?php if ($goal['Goal']['user_id'] != $this->Session->read('Auth.User.id') && isset($goal['Goal'])): //ゴールのリーダが自分以外の場合に表示?>
                                <div class="col col-xxs-12 mt_5px">
                                    <?php $follow_opt = $this->Goal->getFollowOption($goal) ?>
                                    <?php $collabo_opt = $this->Goal->getCollaboOption($goal) ?>
                                    <div class="col col-xxs-6 col-xs-4 mr_5px">
                                        <a class="btn btn-white font_verydark bd-circle_22px toggle-follow p_8px <?= h($follow_opt['class']) ?>"
                                           href="#"
                                           data-class="toggle-follow"
                                           goal-id="<?= $goal['Goal']['id'] ?>"
                                            <?php if ($follow_opt['disabled'] || $this->Goal->isCoachingUserGoal($goal,
                                                    viaIsSet($my_coaching_users))
                                            ): ?>
                                                disabled="disabled"
                                            <?php endif ?>
                                        >
                                            <i class="fa fa-heart font_rougeOrange"
                                               style="<?= h($follow_opt['style']) ?>"></i>
                                            <span class="ml_5px"><?= h($follow_opt['text']) ?></span>
                                        </a>
                                    </div>
                                    <div class="col col-xxs-5 col-xs-4">
                                        <a class="btn btn-white bd-circle_22px font_verydark collaborate-button modal-ajax-get-collab p_8px <?= h($collabo_opt['class']) ?>"
                                           data-toggle="modal"
                                           data-target="#ModalCollabo_<?= $goal['Goal']['id'] ?>"
                                           href="#"
                                           data-url="<?= $this->Html->url([
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
                            <div class="col col-xxs-12 mt_5px">
                                <ul class="profile-user-actions">
                                    <?php if ($is_mine && $goal['Goal']['term_type'] == GoalService::TERM_TYPE_CURRENT): ?>
                                        <li class="profile-user-action-list">
                                            <a class="profile-user-add-action" href="/goals/add_action/goal/<?= $goal['Goal']['id'] ?>"><i class="fa fa-plus"></i>

                                                <p class="profile-user-add-action-text "><?= __("Action") ?></p>

                                                <p class="profile-user-add-action-text "><?= __("Add") ?></p>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    <?php foreach ($goal['ActionResult'] as $key => $action): ?>
                                        <?php
                                        $last_many = false;
                                        //urlはアクション単体ページ
                                        $url = [
                                            'controller' => 'posts',
                                            'action'     => 'feed',
                                            'post_id'    => $action['Post'][0]['id']
                                        ];
                                        //最後の場合かつアクション件数合計が表示件数以上の場合
                                        if ($key == count($goal['ActionResult']) - 1 && count($goal['ActionResultCount']) > $display_action_count) {
                                            $last_many = true;
                                            //urlはゴールページの全アクションリスト
                                            $url = [
                                                'controller' => 'users',
                                                'action'     => 'view_actions',
                                                'user_id'    => $user['User']['id'],
                                                'page_type'  => 'image',
                                                'goal_id'    => $goal['Goal']['id']
                                            ];//TODO urlはマイページのアクションリストが完成したら差し替え
                                        }
                                        ?>
                                        <?php if (Hash::get($action, 'Post.0.id') or $last_many): ?>

                                        <li class="profile-user-action-list">
                                            <a href="<?= $this->Html->url($url) ?>" class="profile-user-action-pic">
                                                <?php if (Hash::get($action, 'ActionResultFile.0.AttachedFile')): ?>
                                                    <?= $this->Html->image('pre-load.svg',
                                                        [
                                                            'class'         => 'lazy',
                                                            'width'         => "48px",
                                                            'height'        => "48px",
                                                            'data-original' => $this->Upload->uploadUrl($action['ActionResultFile'][0]['AttachedFile'],
                                                                "AttachedFile.attached",
                                                                ['style' => 'x_small']),
                                                        ]
                                                    );
                                                    ?>

                                                <?php else: ?>

                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                        <?php
                                                        if (!empty($action["photo{$i}_file_name"]) || $i == 5) {
                                                            echo $this->Html->image('pre-load.svg',
                                                                [
                                                                    'class'         => 'lazy',
                                                                    'width'         => "48px",
                                                                    'height'        => "48px",
                                                                    'data-original' => $this->Upload->uploadUrl($action,
                                                                        "ActionResult.photo$i",
                                                                        ['style' => 'x_small']),
                                                                ]);
                                                            break;
                                                        }
                                                        ?>
                                                    <?php endfor; ?>
                                                <?php endif; ?>
                                                <?php if ($last_many): ?>
                                                    <span class="action-more-counts">
                                                        <i class="fa fa-plus"></i>
                                                        <?= count($goal['ActionResultCount']) - $display_action_count + 1 ?>
                                                    </span>
                                                <?php endif; ?>
                                            </a>
                                        </li>
                                        <?php endif; ?>
                                    <?php endforeach ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach ?>
            <?php if ($unauthorized_goals_count > 0) : ?>
                <div class="col col-xxs-12 unauthorized-goals-notification">
                    <?= __("There is %s goals you do not have permission to see", $unauthorized_goals_count) ?>
                </div>
            <?php endif ?>
        </div>
    </div>
</div>
<?= $this->App->viewEndComment() ?>

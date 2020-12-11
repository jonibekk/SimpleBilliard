<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 7/9/15
 * Time: 3:50 PM
 *
 * @var $goal
 * @var $action_count
 * @var $member_count
 * @var $follower_count
 * @var $is_leader
 * @var $is_coaching_goal
 */
?>
<?= $this->App->viewStartComment() ?>
<div class="panel-body goal-detail-upper-panel">
    <div class="goal-detail-upper-panel-main">
        <div class="goal-detail-avatar-wrap col-sm-3 col-xs-2 col-xxs-3">
            <?=
            $this->Html->image('pre-load.svg',
                [
                    'class'         => 'goal-detail-avatar lazy',
                    'data-original' => $this->Upload->uploadUrl($goal['Goal'], 'Goal.photo',
                        ['style' => 'x_large']),
                ]
            )
            ?>
        </div>
        <div class="goal-detail-upper-panel-main-flex">
            <div class="goal-detail-goal-name-wrap">
                <h5 class="goal-detail-goal-name-top-section">
                    <?= h($goal['Goal']['name']) ?>
                </h5>
            </div>
            <div class="goal-detail-button-wrap mt_18px">
                <?php if ($is_leader): ?>
                    <div class="col col-xxs-9 col-xs-10">
                        <?= $this->Html->link(__('Edit a Goal'),
                            '/goals/' . $goal['Goal']['id'] . '/edit',
                            [
                                'class'    => 'btn btn-white',
                                'disabled' => $isGoalAfterCurrentTerm ? false : true,
                            ])
                        ?>
                    </div>
                    <div class="col col-xxs-3 col-xs-2">
                        <a class="btn btn-white btn-ellipsis dropdown-toggle" data-toggle="dropdown"><span
                                    class="fa fa-ellipsis-h"></span></a>
                        <ul class="dropdown-menu pull-right" role="menu">
                            <li>
                                <?=
                                $this->Form->postLink(__("Delete Goal"),
                                    ['controller' => 'goals', 'action' => 'delete', 'goal_id' => $goal['Goal']['id']],
                                    ['escape' => false], __("Do you really want to delete this Goal?")) ?>
                            </li>
                            <?php if ($isCanComplete): ?>
                                <li>
                                    <?=
                                    $this->Form->postLink(__("Achieve Goal"),
                                        "/goals/complete/" . $goal['Goal']['id'],
                                        ['escape' => false], __("Do you really want to complete this Goal?")) ?>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                <?php else: ?>
                    <?php
                    $follow_opt = $this->Goal->getFollowOption($goal, $goalTerm);
                    $collabo_opt = $this->Goal->getCollaboOption($goal, $goalTerm);
                    ?>
                    <div class="col col-xxs-6">
                        <a class="btn btn-white bd-circle_22px toggle-follow p_8px width100_per
                            <?= h($follow_opt['class']) ?>
                            href=" #"
                        data-class="toggle-follow"
                        goal-id="<?= $goal['Goal']['id'] ?>"
                        <?php if ($follow_opt['disabled'] || $is_coaching_goal): ?>
                            disabled="disabled"
                        <?php endif ?>
                        >
                        <span class="ml_5px"><?= $follow_opt['text'] ?></span>
                        </a>
                    </div>
                    <div class="col col-xxs-6">
                        <a class="btn btn-white bd-circle_22px modal-ajax-get-collab collaborate-button p_8px width100_per
                            <?= h($collabo_opt['class']) ?>"
                           data-toggle="modal"
                           data-target="#ModalCollabo_<?= $goal['Goal']['id'] ?>"
                           href="#"
                            <?php if ($collabo_opt['disabled']): ?>
                                disabled="disabled"
                            <?php endif ?>
                           data-url="<?= $this->Html->url([
                               'controller' => 'goals',
                               'action'     => 'ajax_get_collabo_change_modal',
                               'goal_id'    => $goal['Goal']['id']
                           ]) ?>">
                            <span class=""><?= $collabo_opt['text'] ?></span>
                        </a>
                    </div>
                <?php endif ?>
            </div>
        </div>
    </div>
    <div class="goal-detail-upper-panel-detail">
        <div class="goal-detail-more-details-wrap">
            <a href="#" class="goal-detail-more-details-link js-open-goal-details-info"><span
                        class="fa fa-info-circle"></span>&nbsp;<?= h(__('View info')) ?></a>
        </div>
        <div class="goal-detail-more-details-info col-xxs-12">
            <ul class="goal-detail-items">
                <li class="goal-detail-goal-category">
                    <?= h($goal['GoalCategory']['name']) ?>
                </li>
                <li class="goal-detail-goal-labels">

                    <?php if (!empty($goal['goal_labels'])): ?>
                        <ul class="gl-labels">
                            <?php foreach ($goal['goal_labels'] as $label): ?>
                                <li class="gl-labels-item">
                                    <a href="/goals?labels[]=<?= $label['name'] ?>"
                                       target="<?= $is_mb_app ? "_self" : "_blank" ?>"><?= $label['name'] ?>
                                    </a>
                                </li>
                            <?php endforeach ?>
                        </ul>
                    <?php else: ?>
                        <?= __('No Labels') //TODO 既存のゴール対策。現行のゴールではラベルは必須項目                 ?>
                    <?php endif; ?>
                </li>
                <li class="goal-detail-goal-date">
                    <?= AppUtil::dateYmdReformat($goal['Goal']['start_date'], '/') ?>
                    - <?= AppUtil::dateYmdReformat($goal['Goal']['end_date'], '/') ?>
                    <?php if ($this->Session->read('Auth.User.timezone') != $goalTerm['timezone']): ?>
                        <?= $this->TimeEx->getTimezoneText($goalTerm['timezone']); ?>
                    <?php endif ?>
                </li>
                <li class="goal-detail-goal-description">
                    <p><?= __('Description') ?></p>
                    <p><?= nl2br($this->TextEx->autoLink($goal['Goal']['description'])) ?></p>
                </li>
                <li class="goal-detail-goal-groups">
                    <?php if (empty($goalGroups)) : ?>
                        <p><?= __('This goal is open to all team members') ?></p>
                    <?php else : ?>
                        <p><?= __('Groups that can see this goal') ?></p>
                        <p>
                        <?php foreach ($goalGroups as $group): ?>
                            <a href="#"
                               data-url="<?= $this->Html->url([
                                   'controller' => 'groups',
                                   'action'     => 'ajax_get_group_members',
                                   'group_id'    => $group['id']
                               ]) ?>"
                               class="modal-ajax-get">
                                <span><?= $group['name'] ?></span>
                            </a>
                        <?php endforeach ?>
                        </p>
                    <?php endif ?>
                    <?php if (!empty($archivedGoalGroups)) : ?>
                        <p>
                            <a href="#" class="archived-toggle"><?= __('View archived groups') ?></a>
                        </p>
                        <p class="archived-list">
                            <?php foreach ($archivedGoalGroups as $group): ?>
                                <a href="#"
                                   data-url="<?= $this->Html->url([
                                       'controller' => 'groups',
                                       'action'     => 'ajax_get_group_members',
                                       'group_id'    => $group['id']
                                   ]) ?>"
                                   class="modal-ajax-get">
                                    <span><?= $group['name'] ?></span>
                                </a>
                            <?php endforeach ?>
                        </p>
                    <?php endif ?>
                </li>
                <li class="goal-detail-info-followers">
                    <p><?= __('Followers') . ' (' . count($followers) . ')'; ?></p>
                    <?php
                    $follower_view_num = 5;
                    $iterator = $follower_view_num;
                    $over_num = count($followers) - $follower_view_num + 1;
                    ?>
                    <?php foreach ($followers as $follower): ?>
                        <?php
                        if ($iterator == 0 || ($over_num > 1 && $iterator == 1)) {
                            break;
                        }
                        ?>
                        <?=
                        $this->Html->link($this->Upload->uploadImage($follower['User'], 'User.photo',
                            ['style' => 'medium'],
                            ['class' => 'goal-detail-info-avatar',]),
                            [
                                'controller' => 'users',
                                'action'     => 'view_goals',
                                'user_id'    => $follower['User']['id']
                            ],
                            ['escape' => false]
                        )
                        ?>
                        <?php $iterator--; ?>
                    <?php endforeach ?>

                </li>
            </ul>
        </div>
    </div>
</div>
<div class="goal-detail-tab-group">
    <a class="col-xxs-4 col-xs-2 col-xs-offset-2 col-sm-2 col-sm-offset-2 goal-detail-kr-tab <?= $this->request->params['action'] == 'view_krs' ? "goal-details-tab-active" : null ?>"
       href="<?= $this->Html->url(
           [
               'controller' => 'goals',
               'action'     => 'view_krs',
               'goal_id'    => $goal['Goal']['id'],
           ]); ?>">
        <div class="goal-detail-numbers-kp-counts">
            <?= h($this->NumberEx->formatHumanReadable($kr_count, ['convert_start' => 10000])) ?>
        </div>
        <p class="goal-detail-tab-title">
            <?= h(__('KR')) ?>
        </p>
    </a>
    <a class="col-xxs-4 col-xs-4 col-sm-2 col-sm-offset-1 goal-detail-action-tab <?= $this->request->params['action'] == 'view_actions' ? "goal-details-tab-active" : null ?>"
       href="<?= $this->Html->url(
           [
               'controller' => 'goals',
               'action'     => 'view_actions',
               'goal_id'    => $goal['Goal']['id'],
               'page_type'  => 'image'
           ]); ?>">
        <div class="goal-detail-numbers-action-counts">
            <?= h($this->NumberEx->formatHumanReadable($action_count, ['convert_start' => 10000])) ?>
        </div>
        <p class="goal-detail-tab-title">
            <?= h(__('Action')) ?>
        </p>
    </a>
    <a class="col-xxs-4 col-xs-2 col-sm-2 col-sm-offset-1 goal-detail-member-tab <?= ($this->request->params['action'] == 'view_members') || ($this->request->params['action'] == 'view_followers') ? "goal-details-tab-active" : null ?>"
       href="<?= $this->Html->url(
           [
               'controller' => 'goals',
               'action'     => 'view_members',
               'goal_id'    => $goal['Goal']['id'],
           ]); ?>">
        <div class="goal-detail-numbers-member-counts">
            <?= h($this->NumberEx->formatHumanReadable($member_count, ['convert_start' => 10000])) ?>
        </div>
        <p class="goal-detail-tab-title">
            <?= __('Members') ?>
        </p>
    </a>
</div>
<?= $this->App->viewEndComment() ?>
<?php $this->append('script') ?>
    <script type="text/javascript">
        $(document).ready(function () {
            var archivedHidden = true;
            var archivedToggle = $('.goal-detail-goal-groups .archived-toggle');
            var archivedList = $('.goal-detail-goal-groups .archived-list');

            archivedToggle.click(function(e) {
                e.preventDefault();
                

                if (archivedHidden) {
                    archivedToggle.text("<?= __("Hide archived groups") ?>");
                    archivedList.css("display", "block");
                } else {
                    archivedToggle.text("<?= __("View archived groups") ?>");
                    archivedList.css("display", "none");
                }

                archivedHidden = !archivedHidden
            })
        });
    </script>
<?php $this->end() ?>

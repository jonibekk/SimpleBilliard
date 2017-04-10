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
    <div class="goal-detail-goal-name-wrap col-sm-9 col-xs-10 col-xxs-9">
        <h5 class="goal-detail-goal-name-top-section">
            <?= h($goal['Goal']['name']) ?>
        </h5>
    </div>
    <?php if(strlen($goal['Goal']['name'])<100){ ?>
        <div class="goal-detail-more-details-wrap col-xxs-9">
    <?php }else{ ?>
        <div class="goal-detail-more-details-wrap col-12 col-xs-9 col-xxs-3">
    <?php } ?>
        <a href="#" class="goal-detail-more-details-link js-open-goal-details-info"><span class="fa fa-angle-double-down"></span>&nbsp;<?= h(__('Basic info')) ?></a>
    </div>
    <div class="goal-detail-more-details-info col-xxs-12">
        <ul class="goal-detail-items">
            <li class="goal-detail-goal-category">
                <?= h($goal['GoalCategory']['name']) ?>
            </li>
            <li class="goal-detail-goal-labels">
                <?php if (!empty($goalLabels)): ?>
                    <?php foreach ($goalLabels as $label): ?>
                        <span class="goal-detail-label">
                            <a href="/goals?labels[]=<?= $label['name'] ?>"
                                target="<?= $is_mb_app ? "_self" : "_blank" ?>"><?= $label['name'] ?>
                            </a>
                        </span>
                    <?php endforeach ?>
                <?php else: ?>
                    <?= __('No Labels') //TODO 既存のゴール対策。現行のゴールではラベルは必須項目       ?>
                <?php endif; ?>
            </li>
            <li class="goal-detail-goal-date">
                <?= $this->Time->format('Y/m/d', $goal['Goal']['start_date'] + $goalTerm['timezone'] * HOUR) ?>
                - <?= $this->Time->format('Y/m/d', $goal['Goal']['end_date'] + $goalTerm['timezone'] * HOUR) ?>
                <?php if ($this->Session->read('Auth.User.timezone') != $goalTerm['timezone']): ?>
                    <?= $this->TimeEx->getTimezoneText($goalTerm['timezone']); ?>
                <?php endif ?>
            </li>
            <li class="goal-detail-goal-members">
                <p><?= __('Members') ?></p>
                <div class="goal-detail-info-members">
                    <?php
                    //+1という表示にはせず+2以上の表示にする
                    $member_all = array_merge($goal['Leader'], $goal['GoalMember']);
                    //この値は表示するアイテム数、たとえばアイテム数が5で件数が6だった場合は、5つ目の表示は画像の上に+2となる。
                    //アイテム数が6で件数が8だった場合は、6つ目の表示は画像の上に+3となる。
                    //アイテム数が6で件数も同じ場合は、6つ目の表示は通常のユーザ画像表示。
                    $member_view_num = 6;
                    $iterator = $member_view_num;

                    $over_num = count($member_all) - $member_view_num + 1;
                    ?>
                    <?php foreach ($member_all as $member): ?>
                        <?php
                        if ($iterator == 0 || ($over_num > 1 && $iterator == 1)) {
                            break;
                        }
                        ?>
                        <?=
                        $this->Html->link($this->Upload->uploadImage($member['User'], 'User.photo',
                            ['style' => 'medium'],
                            ['class' => 'goal-detail-info-avatar',]),
                            [
                                'controller' => 'users',
                                'action'     => 'view_goals',
                                'user_id'    => $member['User']['id']
                            ],
                            ['escape' => false]
                        )
                        ?>
                        <?php $iterator--; ?>
                    <?php endforeach ?>
                    <?php if ($over_num > 1): ?>
                        <a href="<?= $this->Html->url([
                            'controller' => 'goals',
                            'action'     => 'view_members',
                            'goal_id'    => $goal['Goal']['id']
                        ]) ?>"
                            class="goal-detail-members-remaining">
                            <?= $this->Upload->uploadImage($member_all[$member_view_num - 1]['User'], 'User.photo',
                                ['style' => 'medium'],
                                ['class' => 'goal-detail-info-avatar',]) ?>
                            <span class="goal-detail-member-more-counts">
                            <i class="fa fa-plus"></i>
                                <?= $over_num ?></span>
                        </a>
                    <?php endif ?>
                </div>
            </li>
            <li class="goal-detail-goal-description">
                <p><?= __('Description') ?></p>
                <p><?= nl2br($this->TextEx->autoLink($goal['Goal']['description'])) ?></p>
            </li>
        </ul>
    </div>
    <div class="goal-detail-button-wrap col-12 col-xs-10 col-sm-12 mt_18px">
        <?php if ($is_leader): ?>
            <?= $this->Html->link(__('Edit a goal'),
                '/goals/' . $goal['Goal']['id'] . '/edit',
                [
                    'class' => 'btn-profile-edit'
                ])
            ?>
        <?php else: ?>
                <?php $follow_opt = $this->Goal->getFollowOption($goal); ?>
                <?php $collabo_opt = $this->Goal->getCollaboOption($goal); ?>
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
                    <span class="ml_5px"><?= __('Follow') ?></span>
                    </a>
                </div>
                <div class="col col-xxs-6">
                    <a class="btn btn-white bd-circle_22px modal-ajax-get-collabo collaborate-button p_8px width100_per
                    <?= h($collabo_opt['class']) ?>"
                       data-toggle="modal"
                       data-target="#ModalCollabo_<?= $goal['Goal']['id'] ?>"
                       href="<?= $this->Html->url([
                           'controller' => 'goals',
                           'action'     => 'ajax_get_collabo_change_modal',
                           'goal_id'    => $goal['Goal']['id']
                       ]) ?>">
                        <span class=""><?= __("Collabo") ?></span>
                    </a>
                </div>
        <?php endif ?>
    </div>


</div>
<div class="goal-detail-tab-group">
    <a class="col-xxs-2 col-xxs-offset-2 goal-detail-kr-tab <?= $this->request->params['action'] == 'view_krs' ? "goal-details-tab-active" : null ?>"
       href="<?= $this->Html->url(
           [
               'controller' => 'goals',
               'action'     => 'view_krs',
               'goal_id'    => $goal['Goal']['id'],
           ]); ?>">
        <div class="goal-detail-numbers-kp-counts">
            <?= h($this->NumberEx->formatHumanReadable($action_count, ['convert_start' => 10000])) ?>
        </div>
        <p class="goal-detail-tab-title">
            <?= h(__('KR')) ?>
        </p>
    </a>
    <a class="col-xxs-2 col-xxs-offset-1 goal-detail-action-tab <?= $this->request->params['action'] == 'view_actions' ? "goal-details-tab-active" : null ?>"
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
    <a class="col-xxs-2 col-xxs-offset-1 goal-detail-member-tab <?= $this->request->params['action'] == 'view_members' ? "goal-details-tab-active" : null ?>"
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

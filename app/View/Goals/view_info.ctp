<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 7/9/15
 * Time: 3:33 PM
 *
 * @var $goal
 * @var $goalTerm
 * @var $goalLabels
 */
?>
<?= $this->App->viewStartComment() ?>
<div class="col-sm-8 col-sm-offset-2">
    <div class="panel panel-default">
        <?= $this->element('Goal/simplex_top_section') ?>
        <div class="panel-body goal-detail-info-panel">
            <ul class="goal-detail-items">
                <li class="goal-detail-goal-name">
                    <?= h($goal['Goal']['name']) ?>
                </li>
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
    </div>
</div>
<?= $this->App->viewEndComment() ?>

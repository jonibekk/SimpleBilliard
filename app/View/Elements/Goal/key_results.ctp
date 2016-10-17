<?php
/**
 * @var $key_results
 * @var $incomplete_kr_count
 * @var $kr_can_edit
 * @var $display_action_count
 * @var $goal_term
 */
?>
<?php if ($key_results): ?>
    <?= $this->App->viewStartComment()?>
    <?php foreach ($key_results as $kr): ?>
        <div class="goal-detail-kr-card">
            <div class="goal-detail-kr-achieve-wrap">
                <?php if ($kr_can_edit): ?>
                    <?php if ($kr['KeyResult']['completed']): ?>
                        <?= $this->Form->postLink('<i class="fa-check-circle fa goal-detail-kr-achieve-already"></i>',
                            ['controller'    => 'goals',
                             'action'        => 'incomplete_kr',
                             'key_result_id' => $kr['KeyResult']['id']
                            ],
                            ['escape' => false, 'class' => 'no-line']) ?>
                    <?php else: ?>
                        <?php //最後のKRの場合
                        if ($incomplete_kr_count === 1):?>
                            <a href="<?= $this->Html->url(['controller'    => 'goals',
                                                           'action'        => 'ajax_get_last_kr_confirm',
                                                           'key_result_id' => $kr['KeyResult']['id']
                            ]) ?>"
                               class="modal-ajax-get no-line">
                                <i class="fa-check-circle fa goal-detail-kr-achieve-yet"></i>
                            </a>
                        <?php else: ?>
                            <?=
                            $this->Form->create('Goal', [
                                'url'           => [
                                    'controller'    => 'goals',
                                    'action'        => 'complete_kr',
                                    'key_result_id' => $kr['KeyResult']['id']
                                ],
                                'inputDefaults' => [
                                    'div'       => 'form-group',
                                    'label'     => false,
                                    'wrapInput' => '',
                                ],
                                'class'         => 'form-feed-notify',
                                'name'          => 'kr_achieve_' . $kr['KeyResult']['id'],
                                'id'            => 'kr_achieve_' . $kr['KeyResult']['id']
                            ]); ?>
                            <?php $this->Form->unlockField('socket_id') ?>
                            <?= $this->Form->end() ?>
                            <a href="#" form-id="kr_achieve_<?= $kr['KeyResult']['id'] ?>"
                               class="kr_achieve_button no-line">
                                <i class="fa-check-circle fa goal-detail-kr-achieve-yet"></i>
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <div class="goal-detail-kr-cards-contents">
                <h4 class="goal-detail-kr-card-title"><?= h($kr['KeyResult']['name']) ?></h4>
                <?php if ($kr['KeyResult']['completed']): ?>
                    <?= __('Clear') ?>
                <?php endif ?>
                <div class="goal-detail-kr-score">
                    <i class="fa fa-bullseye"></i>
                    <?= $kr['KeyResult']['display_value'] ?>
                </div>
                <i class="fa fa-calendar"></i>
                <?= $this->Time->format('Y/m/d', $kr['KeyResult']['start_date'] + $goal_term['timezone'] * HOUR) ?>
                →
                <?= $this->Time->format('Y/m/d', $kr['KeyResult']['end_date'] + $goal_term['timezone'] * HOUR) ?>
                <?php if ($this->Session->read('Auth.User.timezone') != $goal_term['timezone']): ?>
                    <?= $this->TimeEx->getTimezoneText($goal_term['timezone']); ?>
                <?php endif ?>
            </div>
            <?php if ($kr_can_edit): ?>
                <?= $this->element('Goal/key_result_edit_menu_dropdown', ['kr' => $kr]) ?>
            <?php endif ?>
            <ul class="goal-detail-action">
                <?php if ($kr_can_edit): ?>
                    <li class="goal-detail-action-list">
                        <a class="goal-detail-add-action"
                           href="<?= $this->Html->url([
                               'controller'    => 'goals',
                               'action'        => 'add_action',
                               'goal_id'       => $kr['KeyResult']['goal_id'],
                               'key_result_id' => $kr['KeyResult']['id'],
                           ]) ?>"><i
                                class="fa fa-plus"></i>

                            <p class="goal-detail-add-action-text "><?= __("Action") ?></p>

                            <p class="goal-detail-add-action-text "><?= __("Add") ?></p>
                        </a>
                    </li>
                <?php endif; ?>
                <?php foreach ($kr['ActionResult'] as $key => $action): ?>
                    <?php
                    $last_many = false;
                    //urlはアクション単体ページ
                    $url = ['controller' => 'posts', 'action' => 'feed', 'post_id' => $action['Post'][0]['id']];
                    //最後の場合かつアクション件数合計が表示件数以上の場合
                    if ($key == count($kr['ActionResult']) - 1 && $kr['KeyResult']['action_result_count'] > $display_action_count) {
                        $last_many = true;
                        //urlはゴールページの全アクションリスト
                        $url = ['controller'    => 'goals',
                                'action'        => 'view_actions',
                                'goal_id'       => $kr['KeyResult']['goal_id'],
                                'page_type'     => 'image',
                                'key_result_id' => $kr['KeyResult']['id']
                        ];
                    }
                    ?>
                    <li class="goal-detail-action-list">
                        <a href="<?= $this->Html->url($url) ?>" class="profile-user-action-pic">
                            <?php if (viaIsSet($action['ActionResultFile'][0]['AttachedFile'])): ?>
                                <?= $this->Html->image('ajax-loader.gif',
                                    [
                                        'class'         => 'lazy',
                                        'width'         => 48,
                                        'height'        => 48,
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
                                        echo $this->Html->image('ajax-loader.gif',
                                            [
                                                'class'         => 'lazy',
                                                'width'         => 48,
                                                'height'        => 48,
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
                                    <?= $kr['KeyResult']['action_result_count'] - $display_action_count + 1 ?>
                                                </span>
                            <?php endif; ?>
                        </a>
                    </li>
                <? endforeach ?>
            </ul>
        </div>
    <?php endforeach ?>
    <?= $this->App->viewEndComment()?>
<?php endif ?>

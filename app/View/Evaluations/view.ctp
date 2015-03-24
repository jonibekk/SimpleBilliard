<?
/**
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Pages
 * @since         CakePHP(tm) v 0.10.0.1076
 * @var CodeCompletionView $this
 * @var                    $scoreList
 * @var                    $goalList
 * @var                    $evaluateeId
 */
?>
<!-- START app/View/Evaluations/view.ctp -->

<?= $this->Form->create('Evaluation', [
    'inputDefaults' => [
        'div'       => 'form-group',
        'label'     => [
            'class' => 'col col-xxs-12 col-sm-3 control-label form-label'
        ],
        'wrapInput' => 'col col-sm-8',
        'class'     => 'form-control'
    ],
    'class'         => 'form-horizontal',
    'id'            => 'evaluation-form',
    'url'           => ['controller' => 'evaluations', 'action' => 'add'],
    'data-bv-live'  => "disabled"
]); ?>
<? if (!empty($totalList)): ?>

    <div class="panel panel-default col-sm-8 col-sm-offset-2 clearfix">
        <div class="panel-heading"><?= __d('gl', "トータル評価") ?></div>

        <? foreach($totalList as $total): ?>
            <?=
            $this->Html->image('ajax-loader.gif',
                               [
                                   'class'         => 'lazy comment-img',
                                   'data-original' => $this->Upload->uploadUrl($total['EvaluatorUser'], 'User.photo', ['style' => 'small']),
                               ]
            )
            ?>
            <?= h($total['EvaluatorUser']['display_username']); ?>
            <div class="panel-body eval-view-panel-body">
                <div class="form-group">
                    <div for="#" class="col col-sm-3 eval-view-panel-title"><?= __d('gl', "本人") ?></div>
                    <div class="col col-sm-12">
                        <?=
                        $this->Form->input("0.Evaluation.comment", [
                            'type'                     => 'textarea',
                            'rows'                     => 2,
                            'default'                  => $total['Evaluation']['comment'],
                            'label'                    => __d('gl', "評価コメント"),
                            'placeholder'              => __d('gl', "コメントを書いてください"),
                            'required'                 => false,
                            'data-bv-notempty'         => "true",
                            'data-bv-notempty-message' => "入力必須項目です。"
                        ])
                        ?>
                        <small class="help-block" data-bv-validator="notEmpty" data-bv-for="data[0][Evaluation][comment]"
                               data-bv-result="NOT_VALIDATED" style="display: none;">入力必須項目です。
                        </small>
                        <?=
                        $this->Form->input("0.Evaluation.evaluate_score_id", [
                            'type'                     => 'select',
                            'default'                  => $total['Evaluation']['evaluate_score_id'],
                            'options'                  => $scoreList,
                            'id'                       => '',
                            'label'                    => __d('gl', "評価スコア"),
                            'class'                    => 'form-control col-xxs-3',
                            'wrapInput'                => false,
                            'required'                 => false,
                            'data-bv-notempty'         => "true",
                            'data-bv-notempty-message' => "選択必須項目です。"
                        ]);
                        ?>
                        <small class="help-block" data-bv-validator="notEmpty"
                               data-bv-for="data[0][Evaluation][evaluate_score_id]" data-bv-result="NOT_VALIDATED"
                               style="display: none;">選択必須項目です。
                        </small>
                        <?=
                        $this->Form->input("0.Evaluation.id", [
                            'label' => false,
                            'class' => 'form-control col-xxs-10 mb_12px',
                            'type'  => 'hidden',
                            'value' => $total['Evaluation']['id']
                        ]);
                        ?>
                    </div>
                </div>
            </div>
        <? endforeach; ?>
    </div>
<? endif; ?>

<? $goalIndex = 1; ?>
<? foreach ($goalList as $goalKey => $value): ?>
        <? $value = array_values($value); ?>
        <div class="panel panel-default col-sm-8 col-sm-offset-2 clearfix">
            <div class="panel-heading"><?= __d('gl', "ゴール評価") ?>(<?= $goalIndex ?>/<?= count($goalList) ?>)</div>

            <div class="panel-body eval-view-panel-body">
                <div class="form-group">
                    <div class="col col-xxs-6 col-sm-3">
                        <a href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_goal_detail_modal', $eval['Goal']['id']]) ?>"
                           class="modal-ajax-get">
                            <?=
                            $this->Html->image('ajax-loader.gif',
                                               [
                                                   'class'         => 'lazy img-rounded eval-view-panel-goal-pic',
                                                   'width'         => "128",
                                                   'height'        => "128",
                                                   'alt'           => "ゴール画像",
                                                   'data-original' => $this->Upload->uploadUrl($eval, 'Goal.photo',
                                                                                               ['style' => 'large']),
                                               ]
                            )
                            ?></a>
                    </div>
                    <div class="col-xxs-6">
                        <div><?= h($eval['Goal']['GoalCategory']['name']) ?></div>
                        <div>
                            <a href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_goal_detail_modal', $eval['Goal']['id']]) ?>"
                               class="modal-ajax-get"><p
                                    class="ln_trigger-ff font_verydark"><?= h($eval['Goal']['name']) ?></p></a>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="form-group">
                    <div class="col-xxs-12">
                        <div class="col-xxs-6">
                            <div class="eval-view-result-number">
                                <div style="margin:0 auto;width:100px;">
                                    <a class="develop--forbiddenLink" href="#">
                                        <?= count($eval['Goal']['KeyResult']) ?>
                                    </a>
                                </div>
                            </div>
                            <div class="eval-view-result-text">
                                <div style="margin:0 auto;width:100px;">
                                    <a class="develop--forbiddenLink" href="#">
                                        <?= __d('gl', "成果") ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-xxs-6">
                            <div class="eval-view-action-number">
                                <a class="click-show-post-modal pointer"
                                   id="ActionListOpen_<?= $eval['Goal']['id'] ?>"
                                   href="<?= $this->Html->url(['controller' => 'posts', 'action' => 'ajax_get_goal_action_feed', 'goal_id' => $eval['Goal']['id'], 'type' => Post::TYPE_ACTION, 'user_id' => $evaluateeId]) ?>">
                                    <?= count($eval['Goal']['ActionResult']); ?>
                                </a>
                            </div>
                            <div class="eval-view-action-text">
                                <a class="click-show-post-modal pointer"
                                   id="ActionListOpen_<?= $eval['Goal']['id'] ?>"
                                   href="<?= $this->Html->url(['controller' => 'posts', 'action' => 'ajax_get_goal_action_feed', 'goal_id' => $eval['Goal']['id'], 'type' => Post::TYPE_ACTION, 'user_id' => $evaluateeId]) ?>">
                                    <?= __d('gl', "アクション") ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="form-group">
                    <div for="#" class="col col-xxs-12 eval-view-panel-title">
                        <?= __d('gl', "役割:") ?>
                        <? $role = viaIsSet(Hash::extract($eval, "Goal.MyCollabo.{n}[role]")[0]["role"]); ?>
                        <?= ($role) ? h($role) : "リーダー" ?>
                    </div>
                    <div for="#" class="col col-xxs-12 eval-view-panel-title">
                        <?= __d('gl', "アクション:") ?>
                        <?= $eval['Goal']['action_result_count'] ?>
                    </div>
                    <div for="#" class="col col-xxs-12 eval-view-panel-title">
                        <?= __d('gl', "コラボレータ:") ?>
                        <?= count(Hash::extract($eval, "Goal.MyCollabo.{n}[type=0]")) ?>
                    </div>
                    <div for="#" class="col col-xxs-12 eval-view-panel-title">
                        <?= __d('gl', "進捗:") ?>
                        <?= h($eval['Goal']['progress']) ?>%
                    </div>
                    <div for="#" class="col col-xxs-12 eval-view-panel-title">
                        <?= __d('gl', "成果:") ?>
                        <? if (empty($eval['Goal']['KeyResult'])): ?>
                            <?= __d('gl', "なし") ?>
                        <? else: ?>
                            <? foreach ($eval['Goal']['KeyResult'] as $kr): ?>
                                <p><?= h($kr['name']) ?></p>
                            <? endforeach; ?>
                        <? endif; ?>
                    </div>
                    <div for="#" class="col col-xxs-12 eval-view-panel-title">
                        <?= __d('gl', "比重:") ?>
                        <? $collaboPriority = viaIsSet(Hash::extract($eval, "Goal.MyCollabo.{n}[role]")[0]["priority"]); ?>
                        <? $priority = ($collaboPriority) ? $collaboPriority : viaIsSet(Hash::extract($eval,
                                                                                                      "Goal.MyCollabo.{n}[!role]")[0]["priority"]) ?>
                        <?= h($priority) ?>
                    </div>
                </div>
                <hr>
                <? foreach($value as $eval): ?>
                <div class="form-group">
                    <div for="#" class="col col-sm-3 eval-view-panel-title"><?= __d('gl', "本人") ?></div>

                    <div class="col col-sm-12">
                        <?=
                        $this->Form->input("{$key}.Evaluation.comment", [
                            'type'                     => 'textarea',
                            'rows'                     => 2,
                            'default'                  => $eval['Evaluation']['comment'],
                            'label'                    => __d('gl', "評価コメント"),
                            'placeholder'              => __d('gl', "コメントを書いてください"),
                            'required'                 => false,
                            'data-bv-notempty'         => "true",
                            'data-bv-notempty-message' => "入力必須項目です。"
                        ])
                        ?>
                        <small class="help-block" data-bv-validator="notEmpty"
                               data-bv-for="data[<?= $key ?>][Evaluation][comment]" data-bv-result="NOT_VALIDATED"
                               style="display: none;">入力必須項目です。
                        </small>
                        <?=
                        $this->Form->input("{$key}.Evaluation.evaluate_score_id", [
                            'type'                     => 'select',
                            'default'                  => $eval['Evaluation']['evaluate_score_id'],
                            'options'                  => $scoreList,
                            'label'                    => __d('gl', "評価スコア"),
                            'class'                    => 'form-control col-xxs-3',
                            'wrapInput'                => false,
                            'required'                 => false,
                            'data-bv-notempty'         => "true",
                            'data-bv-notempty-message' => "選択必須項目です。"
                        ]);
                        ?>
                        <small class="help-block" data-bv-validator="notEmpty"
                               data-bv-for="data[<?= $key ?>][Evaluation][evaluate_score_id]" data-bv-result="NOT_VALIDATED"
                               style="display: none;">選択必須項目です。
                        </small>
                        <?=
                        $this->Form->input("{$key}.Evaluation.id", [
                            'label' => false,
                            'class' => 'form-control col-xxs-10 mb_12px',
                            'type'  => 'hidden',
                            'value' => $eval['Evaluation']['id']
                        ]);
                        ?>
                    </div>
                </div>
                <? endforeach ?>
            </div>

        </div>
    <? $goalIndex++ ?>
<? endforeach ?>

<div>

    <?= $this->Form->button(__d('gl', "評価登録"), [
        'div'   => false,
        'class' => 'btn btn-primary pull-right',
        'id'    => 'evaluation-register-submit',
        'name'  => 'is_register',
        'value' => true
    ]); ?>
    <?= $this->Form->button(__d('gl', "下書き保存"), [
        'div'   => false,
        'class' => 'btn pull-right',
        'id'    => 'evaluation-draft-submit',
        'name'  => 'is_draft',
        'value' => true
    ]); ?>
    <?= $this->Form->end(); ?>

    <div class="clearfix"></div>
</div>

<!-- END app/View/Evaluations/view.ctp -->

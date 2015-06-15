<?php /**
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Pages
 * @since         CakePHP(tm) v 0.10.0.1076
 * @var CodeCompletionView $this
 * @var                    $scoreList
 * @var                    $goalList
 * @var                    $totalList
 * @var                    $total
 * @var                    $evaluateeId
 * @var                    $evaluateTermId
 * @var                    $isEditable
 * @var                    $saveIndex
 * @var                    $status
 * @var                    $evaluateType
 */
?>
<!-- START app/View/Evaluations/view.ctp -->

<?= $this->Form->create('Evaluation', [
    'inputDefaults' => [
        'div'       => 'form-group',
        'label'     => [
            'class' => 'col col-xxs-12 col-sm-4 col-md-3 control-label form-label'
        ],
        'wrapInput' => 'col col-sm-8',
        'class'     => 'form-control'
    ],
    'class'         => 'form-horizontal',
    'id'            => 'evaluation-form',
    'url'           => ['controller' => 'evaluations', 'action' => 'add', 'evaluatee_id' => $evaluateeId, 'evaluate_term_id' => $evaluateTermId],
    'data-bv-live'  => "disabled"
]) ?>

<!---- Total Evaluations ---->

<?php if (!empty($totalList)): ?>

    <div class="panel panel-default col-sm-8 col-sm-offset-2 clearfix">
        <div class="panel-heading"><?= __d('gl', "トータル評価") ?></div>
        <div class="panel-body eval-view-panel-body">
            <?php foreach ($totalList as $total): ?>
                <?php if ($total['Evaluation']['evaluator_user_id'] == $this->Session->read('Auth.User.id') && $isEditable && $total['Evaluation']['evaluate_type'] != Evaluation::TYPE_FINAL_EVALUATOR):
                    ?>
                    <div class="form-group col-xxs-12 mb_32px">
                        <div class="col-xxs-3 col-xs-2 col-md-1">
                            <?php if ($total['Evaluation']['evaluate_type'] == Evaluation::TYPE_FINAL_EVALUATOR): ?>
                                <i class="fa fa-user user-icon fa-3x eval-view-img text-align_c"></i>
                            <?php else: ?>
                                <?=
                                $this->Html->image('ajax-loader.gif',
                                                   [
                                                       'class'         => 'lazy eval-view-img',
                                                       'data-original' => $this->Upload->uploadUrl($total['EvaluatorUser'],
                                                                                                   'User.photo',
                                                                                                   ['style' => 'medium']),
                                                   ]
                                )
                                ?>
                            <?php endif;?>
                        </div>
                        <div class="col-xxs-9">
                            <div class="lh_44px col-xxs-12">
                                <div for="#" class="col-xxs-12 col-sm-4 col-md-3 eval-view-panel-title">
                                    <?= Evaluation::$TYPE[$total['Evaluation']['evaluate_type']]['view'] ?>
                                </div>
                                <div class="col col-xxs-12 col-sm-4 col-md-3">
                                    <?php if ($total['Evaluation']['evaluate_type'] != Evaluation::TYPE_FINAL_EVALUATOR): ?>
                                        <?= h($total['EvaluatorUser']['display_username']) ?>
                                    <?php endif ?>
                                </div>
                            </div>
                            <div class="col col-xxs-12">
                                <?=
                                $this->Form->input("0.Evaluation.id", [
                                    'label' => false,
                                    'class' => 'form-control col-xxs-10 mb_12px',
                                    'type'  => 'hidden',
                                    'value' => $total['Evaluation']['id']
                                ])
                                ?>
                                <?=
                                $this->Form->input("0.Evaluation.comment", [
                                    'type'                     => 'textarea',
                                    'class'                    => 'form-control eva-val',
                                    'rows'                     => 2,
                                    'default'                  => $total['Evaluation']['comment'],
                                    'label'                    => __d('gl',
                                                                      "<i class='fa fa-comment-o mr_2px'></i>評価コメント"),
                                    'placeholder'              => __d('gl', "コメントを書いてください"),
                                    'required'                 => false,
                                    'data-bv-notempty'         => "true",
                                    'data-bv-notempty-message' => __d('gl', "入力必須項目です。")
                                ])
                                ?>
                                <small class="help-block" data-bv-validator="notEmpty"
                                       data-bv-for="data[0][Evaluation][comment]"
                                       data-bv-result="NOT_VALIDATED" style="display: none;"><?= __d('gl',
                                                                                                     "入力必須項目です。") ?>
                                </small>
                            </div>
                            <div class="col-xxs-12">
                                <?=
                                $this->Form->input("0.Evaluation.evaluate_score_id", [
                                    'type'                     => 'select',
                                    'default'                  => $total['Evaluation']['evaluate_score_id'],
                                    'options'                  => $scoreList,
                                    'label'                    => __d('gl', "<i class='fa fa-paw mr_2px'></i>評価スコア"),
                                    'class'                    => 'form-control col-xxs-12 col-sm-8 eva-val',
                                    'wrapInput'                => false,
                                    'required'                 => false,
                                    'data-bv-notempty'         => "true",
                                    'data-bv-notempty-message' => __d('gl', "選択必須項目です。")
                                ])
                                ?>
                                <small class="help-block" data-bv-validator="notEmpty"
                                       data-bv-for="data[0][Evaluation][evaluate_score_id]"
                                       data-bv-result="NOT_VALIDATED"
                                       style="display: none;"><?= __d('gl', "選択必須項目です。") ?>
                                </small>
                            </div>
                        </div>
                    </div>
                    <?php $saveIndex++;
                    ?>

                <?php else:
                    ?>
                    <div class="col-xxs-12  mb_32px">
                        <div class="col-xxs-3 col-xs-2 col-md-1">
                            <?php if ($total['Evaluation']['evaluate_type'] == Evaluation::TYPE_FINAL_EVALUATOR): ?>
                                <i class="fa fa-user user-icon fa-3x eval-view-img text-align_c"></i>
                            <?php else: ?>
                                <?=
                                $this->Html->image('ajax-loader.gif',
                                                   [
                                                       'class'         => 'lazy eval-view-img',
                                                       'data-original' => $this->Upload->uploadUrl($total['EvaluatorUser'],
                                                                                                   'User.photo',
                                                                                                   ['style' => 'medium']),
                                                   ]
                                )
                                ?>
                            <?php endif ?>
                        </div>
                        <div class="col-xxs-9">
                            <div class="lh_44px col-xxs-12">
                                <div for="#" class="col-xxs-12 col-sm-4 col-md-3 eval-view-panel-title">
                                    <?= Evaluation::$TYPE[$total['Evaluation']['evaluate_type']]['view'] ?>
                                </div>
                                <div class="col col-xxs-12 col-sm-4 col-md-3">
                                    <?php if ($total['Evaluation']['evaluate_type'] != Evaluation::TYPE_FINAL_EVALUATOR): ?>
                                        <?= h($total['EvaluatorUser']['display_username']) ?>
                                    <?php endif ?>
                                </div>
                            </div>
                            <div class="form-group col-xxs-12">
                                <label for="0EvaluationComment"
                                       class="col-xxs-12 col-sm-4 col-md-3 control-label form-label">
                                    <?= __d('gl', "<i class='fa fa-comment-o mr_2px'></i>評価コメント") ?>
                                </label>

                                <div class="col col-sm-8">
                                    <?php if ($total['Evaluation']['status'] != Evaluation::TYPE_STATUS_DONE): ?>
                                        <?= __d('gl', "未確定です。") ?>
                                    <?php else: ?>
                                        <?= h($total['Evaluation']['comment']) ?>
                                    <?php endif ?>
                                </div>
                            </div>
                            <div class="form-group col-xxs-12">
                                <label for="0EvaluationComment"
                                       class="col col-xxs-12 col-sm-4 col-md-3 control-label form-label">
                                    <?= __d('gl', "<i class='fa fa-paw mr_2px'></i>評価スコア") ?>
                                </label>

                                <div class="col col-sm-8">
                                    <?php if ($total['Evaluation']['status'] != Evaluation::TYPE_STATUS_DONE): ?>
                                        <?= __d('gl', "未確定です。") ?>
                                    <?php else: ?>
                                        <?= h($total['EvaluateScore']['name']) ?>
                                    <?php endif ?>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <?php if ($isEditable):
            if ($status != Evaluation::TYPE_STATUS_DONE):
                ?>
                <div class="panel-footer clearfix">
                    <div class="disp_ib pull-right">
                        <?= $this->Form->button(__d('gl', "下書き保存"), [
                            'div'   => false,
                            'class' => 'btn btn-default',
                            'id'    => 'evaluation-draft-submit',
                            'name'  => 'status',
                            'value' => Evaluation::TYPE_STATUS_DRAFT,
                        ]) ?>
                    </div>
                </div>
            <?php endif;
        endif;
        ?>

    </div>
<?php endif; ?>

<!--todo ゴールがemptyでないとき表示する -->
<?php if ($isEditable && !empty($goalList)) : ?>
    <div class="panel panel-default col-sm-8 col-sm-offset-2 clearfix bg-info">
        <div class="text-align_c p_8px bg-lightGray">
            <?php if ($status == Evaluation::TYPE_STATUS_DONE):
                ?>
                <?= $this->Form->button(__d('gl', "修正して確定"), [
                'div'   => false,
                'class' => 'btn btn-primary eval-view-btn-submit',
                'id'    => 'evaluation-register-submit',
                'name'  => 'status',
                'value' => Evaluation::TYPE_STATUS_DONE,
            ]) ?>
            <?php else:
                ?>
                <?= $this->Form->button(__d('gl', "下書き保存"), [
                'div'   => false,
                'class' => 'btn btn-default',
                'id'    => 'evaluation-draft-submit',
                'name'  => 'status',
                'value' => Evaluation::TYPE_STATUS_DRAFT,
            ]) ?>
                <?= $this->Form->button(__d('gl', "確定"), [
                'div'      => false,
                'class'    => 'btn btn-primary eval-view-btn-submit',
                'id'       => 'evaluation-register-submit',
                'name'     => 'status',
                'value'    => Evaluation::TYPE_STATUS_DONE,
                'disabled' => true,
            ]) ?>
            <?php endif;
            ?>
        </div>
    </div>
<?php endif; ?>

<!---- Goal Evaluations ---->

<?php $goalIndex = 1 ?>
<?php foreach ($goalList as $goal): ?>
    <?php $goal = array_values($goal) ?>
    <div class="panel panel-default col-sm-8 col-sm-offset-2 clearfix">
        <div class="panel-heading"><?= __d('gl', "ゴール評価") ?>(<?= $goalIndex ?>/<?= count($goalList) ?>)</div>
        <div class="panel-body eval-view-panel-body">
            <div class="form-group col-xxs-12 eval-view-panel-section">
                <div class="col col-xxs-6 col-sm-4">
                    <a href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_goal_detail_modal', 'goal_id' => $goal[0]['Goal']['id']]) ?>"
                       class="modal-ajax-get">
                        <?=
                        $this->Html->image('ajax-loader.gif',
                                           [
                                               'class'         => 'lazy img-rounded eval-view-panel-goal-pic',
                                               'width'         => "128",
                                               'height'        => "128",
                                               'alt'           => __d('gl', "ゴール画像"),
                                               'data-original' => $this->Upload->uploadUrl($goal[0], 'Goal.photo',
                                                                                           ['style' => 'large']),
                                           ]
                        )
                        ?></a>
                </div>
                <div class="col-xxs-6">
                    <div><?= h($goal[0]['Goal']['GoalCategory']['name']) ?></div>
                    <div>
                        <a href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_goal_detail_modal', 'goal_id' => $goal[0]['Goal']['id']]) ?>"
                           class="modal-ajax-get">
                            <p class="font_bold font_verydark"><?= h($goal[0]['Goal']['name']) ?></p>
                        </a>
                    </div>
                </div>
            </div>
            <div class="form-group col-xxs-12 eval-view-panel-section">
                <div class="col-xxs-12">
                    <div class="col-xxs-6">
                        <div class="eval-view-result-number">
                            <div style="margin:0 auto;width:100px;">
                                <a class="develop--forbiddenLink" href="#">
                                    <?= count(Hash::extract($goal, "0.Goal.KeyResult.{n}[progress=100]")) ?>
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
                               id="ActionListOpen_<?= $goal[0]['Goal']['id'] ?>"
                               href="<?= $this->Html->url(['controller' => 'posts', 'action' => 'ajax_get_goal_action_feed', 'goal_id' => $goal[0]['Goal']['id'], 'type' => Post::TYPE_ACTION, 'user_id' => $evaluateeId]) ?>">
                                <?= count($goal[0]['Goal']['ActionResult']) ?>
                            </a>
                        </div>
                        <div class="eval-view-action-text">
                            <a class="click-show-post-modal pointer"
                               id="ActionListOpen_<?= $goal[0]['Goal']['id'] ?>"
                               href="<?= $this->Html->url(['controller' => 'posts', 'action' => 'ajax_get_goal_action_feed', 'goal_id' => $goal[0]['Goal']['id'], 'type' => Post::TYPE_ACTION, 'user_id' => $evaluateeId]) ?>">
                                <?= __d('gl', "アクション") ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group col-xxs-12 eval-view-panel-section">
                <div for="#" class="col col-xxs-12 eval-view-panel-title">
                    <?= __d('gl', "役割:") ?>
                    <?php $role = viaIsSet(Hash::extract($goal[0], "Goal.MyCollabo.{n}[role]")[0]["role"]) ?>
                    <?= ($role) ? h($role) : __d('gl', "リーダー") ?>
                </div>
                <div for="#" class="col col-xxs-12 eval-view-panel-title">
                    <?= __d('gl', "アクション:") ?>
                    <?= $goal[0]['Goal']['action_result_count'] ?>
                </div>
                <div for="#" class="col col-xxs-12 eval-view-panel-title">
                    <?= __d('gl', "コラボレータ:") ?>
                    <?= count(Hash::extract($goal[0], "Goal.MyCollabo.{n}[type=0]")) ?>
                </div>
                <div for="#" class="col col-xxs-12 eval-view-panel-title">
                    <?= __d('gl', "進捗:") ?>
                    <?= h($goal[0]['Goal']['progress']) ?>%
                </div>
                <div for="#" class="col col-xxs-12 eval-view-panel-title">
                    <?= __d('gl', "成果:") ?>
                    <?php if (empty($goal[0]['Goal']['KeyResult'])): ?>
                        <?= __d('gl', "なし") ?>
                    <?php else: ?>
                        <?php foreach (Hash::extract($goal, "0.Goal.KeyResult.{n}[progress=100]") as $kr): ?>
                            <p><?= h($kr['name']) ?></p>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div for="#" class="col col-xxs-12 eval-view-panel-title">
                    <?= __d('gl', "比重:") ?>
                    <?php $collaboPriority = viaIsSet(Hash::extract($goal[0],
                                                                    "Goal.MyCollabo.{n}[role]")[0]["priority"]) ?>
                    <?php $priority = ($collaboPriority) ? $collaboPriority : viaIsSet(Hash::extract($goal[0],
                                                                                                     "Goal.MyCollabo.{n}[!role]")[0]["priority"]) ?>
                    <?= h($priority) ?>
                </div>
            </div>

            <?php foreach ($goal as $evalIndex => $eval): ?>
                <?php if ($eval['Evaluation']['evaluator_user_id'] == $this->Session->read('Auth.User.id') && $isEditable): ?>
                    <div class="col-xxs-12 mb_32px">
                        <div class="col-xxs-3 col-xs-2 col-md-1">
                            <?=
                            $this->Html->image('ajax-loader.gif',
                                               [
                                                   'class'         => 'lazy eval-view-img',
                                                   'data-original' => $this->Upload->uploadUrl($eval['EvaluatorUser'],
                                                                                               'User.photo',
                                                                                               ['style' => 'medium'])
                                               ]
                            )
                            ?>
                        </div>
                        <div class="form-group col-xxs-9">
                            <div class="lh_44px col-xxs-12">
                                <div for="#" class="col-xxs-12 col-sm-4 col-md-3 eval-view-panel-title">
                                    <?= Evaluation::$TYPE[$total['Evaluation']['evaluate_type']]['view'] ?>
                                </div>
                                <div
                                    class="col-xxs-12 col-sm-4 col-md-3"><?= h($eval['EvaluatorUser']['display_username']) ?></div>
                            </div>
                            <div class="col col-xxs-12">
                                <?=
                                $this->Form->input("{$saveIndex}.Evaluation.id", [
                                    'label' => false,
                                    'class' => 'form-control col-xxs-10 mb_12px',
                                    'type'  => 'hidden',
                                    'value' => $eval['Evaluation']['id']
                                ]);
                                ?>
                                <?=
                                $this->Form->input("{$saveIndex}.Evaluation.comment", [
                                    'type'                     => 'textarea',
                                    'rows'                     => 2,
                                    'default'                  => $eval['Evaluation']['comment'],
                                    'label'                    => __d('gl',
                                                                      "<i class='fa fa-comment-o mr_2px'></i>評価コメント"),
                                    'placeholder'              => __d('gl', "コメントを書いてください"),
                                    'required'                 => false,
                                    'class'                    => 'form-control eva-val',
                                    'data-bv-notempty'         => "true",
                                    'data-bv-notempty-message' => __d('gl', "入力必須項目です。")
                                ])
                                ?>
                                <small class="help-block" data-bv-validator="notEmpty"
                                       data-bv-for="data[<?= $evalIndex ?>][Evaluation][comment]"
                                       data-bv-result="NOT_VALIDATED"
                                       style="display: none;"><?= __d('gl', "入力必須項目です。") ?>
                                </small>
                                <?=
                                $this->Form->input("{$saveIndex}.Evaluation.evaluate_score_id", [
                                    'type'                     => 'select',
                                    'default'                  => $eval['Evaluation']['evaluate_score_id'],
                                    'options'                  => $scoreList,
                                    'label'                    => __d('gl', "<i class='fa fa-paw mr_2px'></i>評価スコア"),
                                    'class'                    => 'form-control col-xxs-12 col-sm-8 eva-val',
                                    'wrapInput'                => false,
                                    'required'                 => false,
                                    'data-bv-notempty'         => "true",
                                    'data-bv-notempty-message' => __d('gl', "選択必須項目です。")
                                ])
                                ?>
                                <small class="help-block" data-bv-validator="notEmpty"
                                       data-bv-for="data[<?= $evalIndex ?>][Evaluation][evaluate_score_id]"
                                       data-bv-result="NOT_VALIDATED"
                                       style="display: none;"><?= __d('gl', "選択必須項目です。") ?>
                                </small>
                            </div>
                        </div>
                    </div>
                    <?php $saveIndex++;
                    ?>
                <?php else:
                    ?>
                    <div class="col-xxs-12  mb_32px">
                        <div class="col-xxs-3 col-xs-2 col-md-1">
                            <?=
                            $this->Html->image('ajax-loader.gif',
                                               [
                                                   'class'         => 'lazy eval-view-img',
                                                   'data-original' => $this->Upload->uploadUrl($eval['EvaluatorUser'],
                                                                                               'User.photo',
                                                                                               ['style' => 'medium']),
                                               ]
                            )
                            ?>
                        </div>
                        <div class="col-xxs-9">
                            <div class="lh_44px col-xxs-12">
                                <div for="#" class="col-xxs-12 col-sm-4 col-md-3 eval-view-panel-title">
                                    <?= Evaluation::$TYPE[$total['Evaluation']['evaluate_type']]['view'] ?>
                                </div>
                                <div
                                    class="col col-xxs-12 col-sm-4 col-md-3"><?= h($eval['EvaluatorUser']['display_username']) ?></div>
                            </div>
                            <div class="form-group">
                                <label for="0EvaluationComment"
                                       class="col col-xxs-12 col-sm-4 col-md-3 control-label form-label">
                                    <?= __d('gl', "<i class='fa fa-comment-o mr_2px'></i>評価コメント") ?>
                                </label>

                                <div class="col col-sm-8">
                                    <?php if ($eval['Evaluation']['status'] != Evaluation::TYPE_STATUS_DONE): ?>
                                        <?= __d('gl', "未確定です。") ?>
                                    <?php else: ?>
                                        <?= h($eval['Evaluation']['comment']) ?>
                                    <?php endif ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="0EvaluationComment"
                                       class="col col-xxs-12 col-sm-4 col-md-3 control-label form-label">
                                    <?= __d('gl', "<i class='fa fa-paw mr_2px'></i>評価スコア") ?>
                                </label>

                                <div class="col col-sm-8">
                                    <?php if ($eval['Evaluation']['status'] != Evaluation::TYPE_STATUS_DONE): ?>
                                        <?= __d('gl', "未確定です。") ?>
                                    <?php else: ?>
                                        <?= h($eval['EvaluateScore']['name']) ?>
                                    <?php endif ?>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php endif;
                ?>
            <?php endforeach ?>
        </div>
        <?php if ($isEditable):
            if ($status != Evaluation::TYPE_STATUS_DONE):
                ?>
                <div class="panel-footer clearfix">
                    <div class="disp_ib pull-right">
                        <?= $this->Form->button(__d('gl', "下書き保存"), [
                            'div'   => false,
                            'class' => 'btn btn-default',
                            'id'    => 'evaluation-draft-submit',
                            'name'  => 'status',
                            'value' => Evaluation::TYPE_STATUS_DRAFT,
                        ]) ?>
                    </div>
                </div>
            <?php endif;
        endif;
        ?>
    </div>
    <?php $goalIndex++ ?>
<?php endforeach ?>

<?php if ($isEditable): ?>
    <div class="panel panel-default col-sm-8 col-sm-offset-2 clearfix">
        <div class="text-align_c p_8px bg-lightGray">
            <?php if ($status == Evaluation::TYPE_STATUS_DONE):
                ?>
                <?= $this->Form->button(__d('gl', "修正して確定"), [
                'div'   => false,
                'class' => 'btn btn-primary eval-view-btn-submit',
                'id'    => 'evaluation-register-submit',
                'name'  => 'status',
                'value' => Evaluation::TYPE_STATUS_DONE
            ]) ?>
            <?php else:
                ?>
                <?= $this->Form->button(__d('gl', "下書き保存"), [
                'div'   => false,
                'class' => 'btn btn-default',
                'id'    => 'evaluation-draft-submit',
                'name'  => 'status',
                'value' => Evaluation::TYPE_STATUS_DRAFT
            ]) ?>
                <?= $this->Form->button(__d('gl', "確定"), [
                'div'      => false,
                'class'    => 'btn btn-primary eval-view-btn-submit',
                'id'       => 'evaluation-register-submit',
                'name'     => 'status',
                'value'    => Evaluation::TYPE_STATUS_DONE,
                'disabled' => true,
            ]) ?>
            <?php endif;
            ?>
        </div>
    </div>
<?php endif; ?>
<?=
$this->Form->input("evaluate_type", [
    'label' => false,
    'class' => 'form-control col-xxs-10 mb_12px',
    'type'  => 'hidden',
    'value' => $evaluateType
])
?>
<?= $this->Form->end() ?>
<!-- END app/View/Evaluations/view.ctp -->

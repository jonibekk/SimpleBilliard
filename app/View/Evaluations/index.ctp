<?
/**
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Pages
 * @since         CakePHP(tm) v 0.10.0.1076
 * @var CodeCompletionView $this
 */
?>
<!-- START app/View/Evaluations/index.ctp -->

<div class="panel panel-default col-sm-8 col-sm-offset-2 clearfix">
    <div class="panel-heading"><?= __d('gl', "トータル評価") ?></div>
    <?=
    $this->Form->create('EvalTotal', [
        'inputDefaults' => [
            'div'       => 'form-group',
            'label'     => [
                'class' => 'col col-xxs-12 col-sm-3 control-label form-label'
            ],
            'wrapInput' => 'col col-sm-6',
            'class'     => 'form-control'
        ],
        'class'         => 'form-horizontal',
        //        'novalidate'    => true,
        //        'id'            => 'UserAccountForm',
    ]); ?>
    <div class="panel-body eval-view-panel-body">
        <div class="form-group">
            <div for="#" class="col col-sm-3 eval-view-panel-title"><?= __d('gl', "本人") ?></div>

            <div class="col col-sm-12">
                <?=
                $this->Form->input('eval_user_comment',
                                   ['label'       => __d('gl', "評価コメント"),
                                    'placeholder' => __d('gl', "コメントを書いてください")
                                   ]),
                $this->Form->input('eval_user_score',
                                   array(
                                       'type'      => 'select',
                                       'options'   => __d('gl', 'ここ何入れるか知らん'),
                                       'value'     => $this->Session->read('current_team_id'),
                                       'id'        => '',
                                       'label'     => __d('gl', "評価スコア"),
                                       'div'       => false,
                                       'class'     => 'form-control col-xxs-3',
                                       'wrapInput' => false,
                                   ))
                ?>
            </div>
        </div>
        <hr>
        <div class="form-group">
            <label for="#" class="col col-sm-3 control-label form-label"><?= __d('gl', "最終者") ?></label>

            <div class="col col-sm-12">
                <?=
                $this->Form->input('eval_user_comment',
                                   ['label'       => __d('gl', "評価コメント"),
                                    'placeholder' => __d('gl', "コメントを書いてください")
                                   ]),
                $this->Form->input('eval_user_score',
                                   array(
                                       'type'      => 'select',
                                       'options'   => __d('gl', 'ここ何入れるか知らん'),
                                       'value'     => $this->Session->read('current_team_id'),
                                       'id'        => '',
                                       'label'     => __d('gl', "評価スコア"),
                                       'div'       => false,
                                       'class'     => 'form-control col-xxs-3',
                                       'wrapInput' => false,
                                   ))
                ?>
            </div>
        </div>
    </div>
    <div class="panel-footer setting_pannel-footer">

        <?= $this->Form->submit(__d('gl', "変更を保存"), ['class' => 'btn btn-primary pull-right']) ?>

        <div class="clearfix"></div>
    </div>
    <?= $this->Form->end(); ?>
</div>
<div class="panel panel-default col-sm-8 col-sm-offset-2 clearfix">
    <div class="panel-heading"><?= __d('gl', "ゴール評価") ?></div>
    <?=
    $this->Form->create('EvalGoals', [
        'inputDefaults' => [
            'div'       => 'form-group',
            'label'     => [
                'class' => 'col col-xxs-12 col-sm-3 control-label form-label'
            ],
            'wrapInput' => 'col col-sm-6',
            'class'     => 'form-control'
        ],
        'class'         => 'form-horizontal',
        //        'novalidate'    => true,
        //        'id'            => 'UserAccountForm',
    ]); ?>
    <div class="panel-body eval-view-panel-body">
        <div class="form-group">
            <div class="col col-xxs-6 col-sm-3">
                <img src="../../img/logo_on.png" width="128" height="128" alt="ゴール画像" class="eval-view-panel-goal-pic">
            </div>
            <div class="col-xxs-6">
                <div>職務とか</div>
                <div>ゴール名</div>
            </div>
        </div>
        <hr>
        <div class="form-group">
            <div class="col-xxs-12">
                <div class="col-xxs-6">
                    <div class="eval-view-result-number"><?= __d('gl', "10") ?></div>
                    <div class="eval-view-result-text"><?= __d('gl', "成果") ?></div>
                </div>
                <div class="col-xxs-6">
                    <div class="eval-view-action-number"><?= __d('gl', "56") ?></div>
                    <div class="eval-view-action-text"><?= __d('gl', "アクション") ?></div>
                </div>
            </div>
        </div>
        <hr>
        <div class="form-group">
            <div for="#" class="col col-xxs-12 eval-view-panel-title"><?= __d('gl', "役割:") ?></div>
            <div for="#" class="col col-xxs-12 eval-view-panel-title"><?= __d('gl', "アクション:") ?></div>
            <div for="#" class="col col-xxs-12 eval-view-panel-title"><?= __d('gl', "コラボれーた:") ?></div>
            <div for="#" class="col col-xxs-12 eval-view-panel-title"><?= __d('gl', "進捗:") ?></div>
            <div for="#" class="col col-xxs-12 eval-view-panel-title"><?= __d('gl', "比重:") ?></div>
        </div>
        <hr>
        <div class="form-group">
            <div for="#" class="col col-sm-3 eval-view-panel-title"><?= __d('gl', "本人") ?></div>

            <div class="col col-sm-12">
                <?=
                $this->Form->input('eval_user_comment',
                                   ['label'       => __d('gl', "評価コメント"),
                                    'placeholder' => __d('gl', "コメントを書いてください")
                                   ]),
                $this->Form->input('eval_user_score',
                                   array(
                                       'type'      => 'select',
                                       'options'   => __d('gl', 'ここ何入れるか知らん'),
                                       'value'     => $this->Session->read('current_team_id'),
                                       'id'        => '',
                                       'label'     => __d('gl', "評価スコア"),
                                       'div'       => false,
                                       'class'     => 'form-control col-xxs-3',
                                       'wrapInput' => false,
                                   ))
                ?>
            </div>
        </div>
    </div>
    <div class="panel-footer setting_pannel-footer">

        <?= $this->Form->submit(__d('gl', "変更を保存"), ['class' => 'btn btn-primary pull-right']) ?>

        <div class="clearfix"></div>
    </div>
    <?= $this->Form->end(); ?>
</div>
<? $this->append('script') ?>
<? $this->end() ?><!-- END app/View/Evaluations/index.ctp -->

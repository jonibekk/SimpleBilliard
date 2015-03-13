<?
/**
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Pages
 * @since         CakePHP(tm) v 0.10.0.1076
 * @var CodeCompletionView $this
 */
?>
<!-- START app/View/Evaluations/view.ctp -->

<!--
<div class="row">
    <div class="col-sm-8 col-sm-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading"><? /*= __d('gl', "評価画面的タイトル") */ ?></div>
            <div class="panel-body add-team-panel-body">
                評価画面だよ。なる。よろしく(^^)
            </div>
        </div>
    </div>
</div>
-->

<div class="panel panel-default col-sm-8 col-sm-offset-2 clearfix">
    <div class="panel-heading"><?= __d('gl', "トータル評価") ?></div>
    <?=
    $this->Form->create('EvalView', [
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
            <div for="#" class="col col-sm-3"><?= __d('gl', "本人") ?></div>

            <div class="col col-sm-12">
                <?=
                $this->Form->input('eval_user_comment',
                                   ['label'       => __d('gl', "評価コメント"),
                                    'placeholder' => __d('gl', "コメントを書いてください")
                                   ]),
                $this->Form->input('eval_user_score',
                                   array(
                                       'type'      => 'select',
                                       'options'   => __d('gl','ここ何入れるか知らん'),
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
        <div class="form-group">
            <label for="#" class="col col-sm-3 control-label form-label"><?= __d('gl', "本人") ?></label>

            <div class="col col-sm-12">
                <?=
                $this->Form->input('eval_user_comment',
                                   ['label'       => __d('gl', "評価コメント"),
                                    'placeholder' => __d('gl', "コメントを書いてください")
                                   ]),
                $this->Form->input('eval_user_score',
                                   array(
                                       'type'      => 'select',
                                       'options'   => __d('gl','ここ何入れるか知らん'),
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
        <div class="form-group">
            <label for="UserPassword" class="col col-sm-3 control-label form-label"><?= __d('gl', "太郎") ?></label>

            <div class="col col-sm-6">
                <p class="form-control-static">
                    <?= __d('gl', "花子") ?>
                </p>
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
<? $this->end() ?>
<!-- END app/View/Evaluations/view.ctp -->

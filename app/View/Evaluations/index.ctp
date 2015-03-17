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
    <div class="panel-heading"><?= __d('gl', "評価") ?></div>
    <?=
    $this->Form->create('EvalIndex', [
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
        <div class="col-sm-12 bg-warning p_8px mb_8px"><?= __d('gl',"あと1件の評価が完了しておりません。以下より評価を行なってください。") ?></div>
        <hr>
        <div class="form-group">
            <div for="#" class="col col-sm-3 eval-index-panel-title"><?= __d('gl', "自分") ?></div>
            <p><?= __d('gl', "未完了") ?></p>
            <div class="col col-xxs-6 col-sm-3">
                <img src="../../img/logo_on.png" width="64" height="64" alt="You" class="eval-view-panel-goal-pic">
            </div>

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


        <?= $this->Form->submit(__d('gl', "変更を保存"), ['class' => 'btn btn-primary pull-right']) ?>

        <div class="clearfix"></div>
    </div>
    <?= $this->Form->end(); ?>
</div>
<? $this->append('script') ?>
<? $this->end() ?><!-- END app/View/Evaluations/index.ctp -->

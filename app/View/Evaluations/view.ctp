<?
/**
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Pages
 * @since         CakePHP(tm) v 0.10.0.1076
 * @var CodeCompletionView $this
 */
?>
<!-- START app/View/Evaluations/view.ctp -->

<?= $this->Form->create('Evaluation', [
    'inputDefaults' => [
        'div'       => 'form-group',
        'label'     => [
            'class' => 'col col-xxs-12 col-sm-3 control-label form-label'
        ],
        'wrapInput' => 'col col-sm-6',
        'class'     => 'form-control'
    ],
    'class'         => 'form-horizontal',
    'url'           => ['controller' => 'evaluations', 'action' => 'add', $evaluateTermId, $evaluateeId],
    'novalidate'    => true
]); ?>
<? if(!empty($total)): ?>

<div class="panel panel-default col-sm-8 col-sm-offset-2 clearfix">
    <div class="panel-heading"><?= __d('gl', "トータル評価") ?></div>

    <div class="panel-body eval-view-panel-body">
        <div class="form-group">
            <div for="#" class="col col-sm-3 eval-view-panel-title"><?= __d('gl', "本人") ?></div>

            <div class="col col-sm-12">
                <?=
                $this->Form->input("0.Evaluation.comment", [
                    'type' => 'text',
                    'label'       => __d('gl', "評価コメント"),
                    'placeholder' => __d('gl', "コメントを書いてください"),
                    'allow-empty' => $total['Evaluation']['allow_empty']
                ]),
                $this->Form->input("0.Evaluation.evaluate_score_id", [
                    'type'      => 'select',
                    'default'   => $total['Evaluation']['evaluate_score_id'],
                    'options'   => $scoreList,
                    'id'        => '',
                    'label'     => __d('gl', "評価スコア"),
                    'div'       => false,
                    'class'     => 'form-control col-xxs-3',
                    'wrapInput' => false,
                ]);
                ?>
            </div>
        </div>

    </div>
</div>
<? endif; ?>
<div class="panel panel-default col-sm-8 col-sm-offset-2 clearfix">
    <div class="panel-heading"><?= __d('gl', "ゴール評価") ?></div>

    <? foreach($goalList as $key => $eval):?>

    <div class="panel-body eval-view-panel-body">
        <div class="form-group">
            <div class="col col-xxs-6 col-sm-3">
                <img src="http://192.168.50.4/upload/users/1/9c75baad22a4cc4f0c3d63a163a2e280_small.jpg?1426140852" width="128" height="128" alt="ゴール画像" class="eval-view-panel-goal-pic">
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
                $this->Form->input("{$key}.Evaluation.comment", [
                    'type' => 'text',
                   'label'       => __d('gl', "評価コメント"),
                   'placeholder' => __d('gl', "コメントを書いてください"),
                   'allow-empty' => $eval['Evaluation']['allow_empty']
                ]),
                $this->Form->input("{$key}.Evaluation.evaluate_score_id", [
                   'type'      => 'select',
                   'default'   => $eval['Evaluation']['evaluate_score_id'],
                   'options'   => $scoreList,
                   'id'        => '',
                   'label'     => __d('gl', "評価スコア"),
                   'div'       => false,
                   'class'     => 'form-control col-xxs-3',
                   'wrapInput' => false,
                ]);
                $this->Form->input("{$key}.Evaluation.id", [
                    'label' => false,
                    'class' => 'form-control col-xxs-10 mb_12px',
                    'type'  => 'hidden',
                    'value' => $eval['Evaluation']['id']
                ]);
                ?>
            </div>
        </div>
    </div>
    <hr>
    <? endforeach ?>

    <div class="panel-footer setting_pannel-footer">

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

        <div class="clearfix"></div>
    </div>
</div>
<? $this->append('script') ?>
<? $this->end() ?>
<!-- END app/View/Evaluations/view.ctp -->

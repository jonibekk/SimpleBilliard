<?
/**
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Pages
 * @since         CakePHP(tm) v 0.10.0.1076
 * @var CodeCompletionView $this
 */
?>
<!-- START app/View/Evaluations/view.ctp -->
<div class="row">
    <div class="col-sm-8 col-sm-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading"><?= __d('gl', "評価画面的タイトル") ?></div>
            <div class="panel-body add-team-panel-body">
                <?= $this->Form->create('Evaluation', [
                    'inputDefaults' => [
                        'div'       => 'form-group mb_5px develop--font_normal',
                        'wrapInput' => false,
                        'class'     => 'form-control',
                    ],
                    'url'           => ['controller' => 'evaluations', 'action' => 'add'],
                ]); ?>
                <? foreach($evaluationList as $eval):?>

                    <?=
                    $this->Form->input('Evaluation.evaluate_score_id', [
                        'label'   => false,
                        'class'   => 'form-control col-xxs-10 mb_12px add-select-options',
                        'type'    => 'select',
                        'options' => $scoreList
                    ]);
                    ?>
                    <?=
                    $this->Form->input('Evaluation.comment', [
                        'label' => false,
                        'class' => 'form-control col-xxs-10 mb_12px',
                    ]);
                    ?>
                <? endforeach ?>
                <?= $this->Form->submit(__d('gl', "下書き保存"), [
                    'div'      => false,
                    'class'    => 'btn btn-info pull-right',
                    'name'     => 'is_draft'
                ]); ?>
                <?= $this->Form->submit(__d('gl', "評価登録"), [
                    'div'      => false,
                    'class'    => 'btn btn-info pull-right',
                    'name'     => 'is_register'
                ]); ?>
            </div>
        </div>
    </div>
</div>
<!-- END app/View/Evaluations/view.ctp -->

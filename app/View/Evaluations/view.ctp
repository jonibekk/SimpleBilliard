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
                <?
                if(empty($evaluationList[0]['Evaluation']['goal_id'])):
                ?>
                    <h4>トータル評価</h4>
                <?
                else:
                ?>
                    <h4>ゴール評価</h4>
                <?
                endif;
                ?>
                <?= $this->Form->create('Evaluation', [
                    'inputDefaults' => [
                        'div'       => 'form-group mb_5px develop--font_normal',
                        'wrapInput' => false,
                        'class'     => '',
                    ],
                    'url'           => ['controller' => 'evaluations', 'action' => 'add'],
                    'novalidate'
                ]); ?>
                <? foreach($evaluationList as $key => $eval):?>
                    <?
                    if($eval['Evaluation']['index'] == 1 && empty($evaluationList[0]['Evaluation']['goal_id'])):
                    ?>
                        <h4>ゴール評価</h4>
                    <?
                    endif;
                    ?>
                    <?=
                    $this->Form->input("{$key}.Evaluation.evaluate_score_id", [
                        'label'   => false,
                        'class'   => 'form-control col-xxs-10 mb_12px',
                        'type'    => 'select',
                        'default' => $eval['Evaluation']['evaluate_score_id'],
                        'options' => $scoreList
                    ]);
                    ?>
                    <?=
                    $this->Form->input("{$key}.Evaluation.comment", [
                        'label' => false,
                        'class' => 'form-control col-xxs-10 mb_12px',
                        'default' => $eval['Evaluation']['comment'],
                    ]);
                    ?>
                    <?=
                    $this->Form->input("{$key}.Evaluation.id", [
                        'label' => false,
                        'class' => 'form-control col-xxs-10 mb_12px',
                        'type'  => 'hidden',
                        'value' => $eval['Evaluation']['id']
                    ]);
                    ?>
                <? endforeach ?>
                <?= $this->Form->button(__d('gl', "下書き保存"), [
                    'div'   => false,
                    'class' => 'btn btn-info pull-right',
                    'name'  => 'is_draft',
                    'value' => true
                ]); ?>
                <?= $this->Form->button(__d('gl', "評価登録"), [
                    'div'   => false,
                    'class' => 'btn btn-info pull-right',
                    'name'  => 'is_register',
                    'value' => true
                ]); ?>
                <?= $this->Form->end(); ?>
            </div>
        </div>
    </div>
</div>
<!-- END app/View/Evaluations/view.ctp -->

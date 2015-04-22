<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/30/14
 * Time: 9:59 AM
 *
 * @var CodeCompletionView $this
 * @var array              $my_teams
 * @var                    $term_start_date
 * @var                    $term_end_date
 * @var                    $eval_enabled
 * @var                    $eval_start_button_enabled
 * @var                    $current_eval_is_available
 * @var                    $current_term_start_date
 * @var                    $current_term_end_date
 * @var                    $current_eval_is_frozen
 * @var                    $current_term_id
 * @var                    $previous_eval_is_available
 * @var                    $previous_term_start_date
 * @var                    $previous_term_end_date
 * @var                    $previous_eval_is_frozen
 * @var                    $previous_term_id
 */
?>
<!-- START app/View/Elements/Team/evaluation_score_setting.ctp -->
<div class="panel panel-default">
    <div class="panel-heading"><?= __d('gl', "評価スコア定義") ?></div>
    <div class="panel-body form-horizontal">
        <?=
        $this->Form->create('EvaluateScore', [
            'inputDefaults' => [
                'div'       => false,
                'label'     => false,
                'wrapInput' => 'col col-sm-9',
            ],
            'class'         => 'form-horizontal',
            'novalidate'    => true,
            'id'            => 'EvaluationSettingForm',
            'url'           => ['controller' => 'teams', 'action' => 'save_evaluation_scores']
        ]); ?>
        <?= $this->Form->hidden('id') ?>
        <?= $this->Form->hidden('team_id', ['value' => $this->Session->read('current_team_id')]) ?>
        <table class="table table-striped" id="EvaluateScoreTable">
            <tr>
                <th>
                    <?php echo __d('gl', '名前') ?>
                </th>
                <th>
                    <?php echo __d('gl', '表示順') ?>
                </th>
                <th>
                    <?php echo __d('gl', '説明') ?>
                </th>
                <th></th>
            </tr>
            <? foreach ($this->request->data['EvaluateScore'] as $es_key => $evaluation_select_value) : ?>
                <?= $this->element('Team/eval_score_form_elm',
                                   ['index' => $es_key, 'id' => $evaluation_select_value['id'], 'type' => 'exists']) ?>
            <?php endforeach; ?>
        </table>
        <div class="form-group">
            <? $index = count($this->request->data['EvaluateScore']);
            $max_index = $index + 9; ?>
            <?= $this->Html->link(__d('gl', "定義を１つ追加"), ['controller' => 'teams', 'action' => 'ajax_get_score_elm'],
                                  ['id' => 'AddScoreButton', 'target-selector' => '#EvaluateScoreTable > tbody', 'index' => $index, 'max_index' => $max_index, 'class' => 'btn btn-default pull-left']) ?>
        </div>
        <? for ($i = $index; $i <= $max_index; $i++): ?>
            <? $this->Form->unlockField("EvaluateScore.$i.name") ?>
            <? $this->Form->unlockField("EvaluateScore.$i.index_num") ?>
            <? $this->Form->unlockField("EvaluateScore.$i.description") ?>
        <? endfor ?>
    </div>
    <div class="panel-footer">
        <div class="row">
            <div class="col-sm-9 col-sm-offset-3">
                <?= $this->Form->submit(__d('gl', '評価スコア設定を保存'), ['class' => 'btn btn-primary pull-right']) ?>
            </div>
        </div>
    </div>

    <?= $this->Form->end() ?>
</div>
<!-- END app/View/Elements/Team/evaluation_score_setting.ctp -->
<? $this->start('script') ?>
<script type="text/javascript">
    $(document).ready(function () {
        $('#EvaluationSettingForm').bootstrapValidator({
            live: 'enabled',
            feedbackIcons: {}
        })
            .on('click', '#AddScoreButton', function (e) {
                e.preventDefault();
                var $obj = $(this);
                var target_selector = $obj.attr("target-selector");
                var index = parseInt($obj.attr("index"));


                $.get($obj.attr('href') + "/index:" + index, function (data) {
                    $(target_selector).append(data);
                    $(data).find("input,textarea").each(function (i, val) {
                        $('#EvaluationSettingForm').bootstrapValidator('addField', $(val).attr('name'));
                    });
                    if ($obj.attr('max_index') != undefined && index >= parseInt($obj.attr('max_index'))) {
                        $obj.attr('disabled', 'disabled');
                    }
                    //increment
                    $obj.attr('index', index + 1);
                });
            });
    });
</script>
<? $this->end() ?>


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
<?= $this->App->viewStartComment()?>
<div class="panel panel-default">
    <div class="panel-heading"><?= __("Evaluation score settings") ?></div>
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
        <table class="table table-striped" id="EvaluateScoreTable">
            <tr>
                <th>
                    <?php echo __('Name') ?>
                </th>
                <th>
                    <?php echo __('Display order') ?>
                </th>
                <th>
                    <?php echo __('Description') ?>
                </th>
                <th></th>
            </tr>
            <?php foreach ($this->request->data['EvaluateScore'] as $es_key => $evaluation_select_value) : ?>
                <?= $this->element('Team/eval_score_form_elm',
                    ['index' => $es_key, 'id' => $evaluation_select_value['id'], 'type' => 'exists']) ?>
            <?php endforeach; ?>
        </table>
        <div class="form-group">
            <?php $index = count($this->request->data['EvaluateScore']);
            $max_index = $index + 9; ?>
            <?= $this->Html->link(__("Add definition"), ['controller' => 'teams', 'action' => 'ajax_get_score_elm'],
                ['id'              => 'AddScoreButton',
                 'target-selector' => '#EvaluateScoreTable > tbody',
                 'index'           => $index,
                 'max_index'       => $max_index,
                 'class'           => 'btn btn-default'
                ]) ?>
            <input class="btn btn-primary team-setting-add-goal-category" type="submit" value="<?= __('Save evaluation score settings')?>">
        </div>
        <?php for ($i = $index; $i <= $max_index; $i++): ?>
            <?php $this->Form->unlockField("EvaluateScore.$i.name") ?>
            <?php $this->Form->unlockField("EvaluateScore.$i.index_num") ?>
            <?php $this->Form->unlockField("EvaluateScore.$i.description") ?>
        <?php endfor ?>
        <?= $this->Form->end() ?>

    </div>
</div>
<?= $this->App->viewEndComment()?>
<?php $this->start('script') ?>
<script type="text/javascript">
    $(document).ready(function () {
        $('#EvaluationSettingForm').bootstrapValidator({
            live: 'enabled'
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
<?php $this->end() ?>

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
<!-- START app/View/Elements/Team/goal_category_setting.ctp -->
<div class="panel panel-default">
    <div class="panel-heading"><?= __d('gl', "ゴールカテゴリ設定") ?></div>
    <div class="panel-body form-horizontal">
        <?=
        $this->Form->create('GoalCategory', [
            'inputDefaults' => [
                'div'       => false,
                'label'     => false,
                'wrapInput' => 'col col-sm-9',
            ],
            'class'         => 'form-horizontal',
            'novalidate'    => true,
            'id'            => 'GoalCategorySettingForm',
            'url'           => ['controller' => 'teams', 'action' => 'save_goal_categories']
        ]); ?>
        <table class="table table-striped" id="GoalCategoryTable">
            <tr>
                <th>
                    <?php echo __d('gl', '名前') ?>
                </th>
                <th>
                    <?php echo __d('gl', '説明') ?>
                </th>
                <th></th>
            </tr>
            <?php foreach ($this->request->data['GoalCategory'] as $es_key => $evaluation_select_value) : ?>
                <?= $this->element('Team/goal_category_form_elm',
                                   ['index' => $es_key, 'id' => $evaluation_select_value['id'], 'type' => 'exists']) ?>
            <?php endforeach; ?>
        </table>
        <div class="form-group">
            <?= $this->Form->submit(__d('gl', 'ゴールカテゴリ設定を保存'), ['class' => 'btn btn-primary pull-right']) ?>
            <?php $index = count($this->request->data['GoalCategory']);
            $max_index = $index + 9; ?>
            <?= $this->Html->link(__d('gl', "ゴールカテゴリを１つ追加"),
                                  ['controller' => 'teams', 'action' => 'ajax_get_goal_category_elm'],
                                  ['id' => 'AddCategoryButton', 'target-selector' => '#GoalCategoryTable > tbody', 'index' => $index, 'max_index' => $max_index, 'class' => 'btn btn-default pull-left']) ?>
        </div>
        <?php for ($i = $index; $i <= $max_index; $i++): ?>
            <?php $this->Form->unlockField("GoalCategory.$i.name") ?>
            <?php $this->Form->unlockField("GoalCategory.$i.description") ?>
        <?php endfor ?>
        <?= $this->Form->end() ?>

    </div>
</div>
<!-- END app/View/Elements/Team/goal_category_setting.ctp -->
<?php $this->start('script') ?>
<script type="text/javascript">
    $(document).ready(function () {
        $('#GoalCategorySettingForm').bootstrapValidator({
            live: 'enabled',
            feedbackIcons: {}
        })
            .on('click', '#AddCategoryButton', function (e) {
                e.preventDefault();
                var $obj = $(this);
                var target_selector = $obj.attr("target-selector");
                var index = parseInt($obj.attr("index"));


                $.get($obj.attr('href') + "/index:" + index, function (data) {
                    $(target_selector).append(data);
                    $(data).find("input,textarea").each(function (i, val) {
                        $('#GoalCategorySettingForm').bootstrapValidator('addField', $(val).attr('name'));
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


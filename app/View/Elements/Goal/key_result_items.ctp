<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 9/26/14
 * Time: 11:14 AM
 *
 * @var CodeCompletionView $this
 * @var                    $key_results
 * @var                    $kr_can_edit
 * @var                    $incomplete_kr_count
 * @var                    $goal_id
 * @var                    $is_init
 */
if (!isset($is_init)) {
    $is_init = false;
}
if (!isset($kr_can_edit)) {
    $kr_can_edit = false;
}
?>

<!-- START app/View/Elements/Goal/key_result_items.ctp -->

<!-- ToDo -> 大樹さん、完了したKRかどうかでアイコンのクラスを変更する処理もお願いします。 -->
<?php
if ($is_init) {
    //初期表示の場合は最大２件
    $limit_count = 2;
    $count = 1;
}
?>
<?php foreach ($key_results as $kr): ?>
    <li class="dashboard-goals-card-body-krs">
        <div class="dropdown dashboard-goals-card-body-kr-dropdown">
            <a href="" class="dashboard-goals-card-body-kr-link" data-toggle="dropdown" id="download">
                <hr class="dashboard-goals-card-horizontal-line">
                <i class="fa-key fa <?= $kr['completed'] ? "dashboard-goals-card-body-krs-icon-achieved" : "dashboard-goals-card-body-krs-icon-unachieved" ?>"></i>

                <p class="<?= $kr['completed'] ? "dashboard-goals-card-body-krs-title-achieved" : "dashboard-goals-card-body-krs-title-unachieved" ?>"><?= h($kr['name']) ?></p>
            </a>
            <?= $this->element('Goal/key_result_edit_button', ['kr' => $kr, 'without_dropdown_link' => true]) ?>
        </div>
        <?php if (!$kr['completed']): ?>
            <a href="#" form-id="kr_achieve_<?= $kr['id'] ?>"
               class="kr_achieve_button fa-check fa dashboard-goals-card-body-krs-action"></a>
            <?=
            $this->Form->create('Goal', [
                'url'           => ['controller' => 'goals', 'action' => 'complete_kr', 'key_result_id' => $kr['id']],
                'inputDefaults' => [
                    'div'       => 'form-group',
                    'label'     => false,
                    'wrapInput' => '',
                ],
                'class'         => 'form-feed-notify',
                'name'          => 'kr_achieve_' . $kr['id'],
                'id'            => 'kr_achieve_' . $kr['id']
            ]); ?>
            <?php $this->Form->unlockField('socket_id') ?>
            <?= $this->Form->end() ?>
        <?php endif; ?>

        <div class="dashboard-goals-card-body-krs-aside">
            <a href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'view_actions', 'goal_id' => $goal_id, 'key_result_id' => $kr['id'], 'page_type' => 'list']) ?>"
               class="dashboard-goals-card-body-add-krs-past-action">
                <i class="fa-check fa dashboard-goals-card-body-krs-past-action-icon"></i>
                <span
                    class="dashboard-goals-card-body-krs-past-action-number"><?= h($kr['action_result_count']) ?></span>
            </a>
            <span class="dashboard-goals-card-body-krs-date"><?= __d('gl',
                                                                     'Due.') ?> <?= $this->TimeEx->dateNoYear($kr['end_date']) ?></span>
        </div>
    </li>

    <?php
    if ($count++ == $limit_count) {
        break;
    }
    ?>
<?php endforeach ?>

<!-- 1>0のときとかいう条件付けて読み込まないようにしてありますｗ -->
<?php if (0 > 1): ?>
    <?php if (!empty($key_results)): ?>
        <?php foreach ($key_results as $kr): ?>
            <div class="bd-t h_50px">
                <div class="col col-xxs-12 responsive-goal-space-width mxh_50px ln_1 ptb_5px">
                    <div class="inline-block responsive-goal-title-width pl_1px">
                        <span class="ln_1 tap-btn-text font_verydark kr-text">
                            <?= h($kr['KeyResult']['name']) ?></span>
                        <i class="fa fa-check-circle"></i>
                        <span class="ml_2px"><?= h($kr['KeyResult']['action_result_count']) ?></span>
                        <?php if ($kr['KeyResult']['completed']): ?>
                            <span class="fin-kr tag-sm tag-info"><?= __d('gl', "完了") ?></span>
                        <?php else: ?>
                            <span class="unfin-kr tag-sm tag-danger"><?= __d('gl', "未完了") ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if ($kr_can_edit): ?>
                    <?= $this->element('Goal/key_result_edit_button', ['kr' => $kr]) ?>
                <?php endif; ?>
            </div>
        <?php endforeach ?>
        <?php if ($kr_can_edit): ?>
            <div class="bd-t pt_8px">
                <a class="col col-xxs-12 bd-dash font_lightGray-gray p_10px modal-ajax-get-add-key-result"
                   href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_add_key_result_modal', 'goal_id' => $goal_id]) ?>">
                    <i class="fa fa-plus-circle font_brownRed"></i>
                    <span class="ml_2px"><?= __d('gl', "達成要素を追加") ?></span>
                </a>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="col col-xxs-12">
            <div class="bd-t pt_8px">
                <a class="col col-xxs-12 bd-dash font_lightGray-gray p_10px modal-ajax-get-add-key-result"
                   href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_add_key_result_modal', 'goal_id' => $goal_id]) ?>">
                    <i class="fa fa-plus-circle font_brownRed"></i>
                    <span class="ml_2px"><?= __d('gl', "達成要素を追加") ?></span>
                </a>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>
<!-- End app/View/Elements/Goal/key_result_items.ctp -->

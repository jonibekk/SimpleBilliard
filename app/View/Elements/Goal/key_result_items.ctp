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
if (isset($key_results[key($key_results)]['KeyResult'])) {
    $key_results = Hash::extract($key_results, '{n}.KeyResult');
}
?>

<!-- START app/View/Elements/Goal/key_result_items.ctp -->
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
            <?= $this->element('Goal/key_result_edit_menu_dropdown', ['kr' => $kr, 'without_dropdown_link' => true]) ?>
        </div>
        <?php if (!$kr['completed']): ?>
            <?php if ($incomplete_kr_count === 1): ?>
                <a href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_last_kr_confirm', 'key_result_id' => $kr['id']]) ?>"
                   class="modal-ajax-get kr_achieve_button fa-check fa dashboard-goals-card-body-krs-action"></a>
            <?php else: ?>
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
    if ($is_init && $count++ == $limit_count) {
        break;
    }
    ?>
<?php endforeach ?>
<!-- End app/View/Elements/Goal/key_result_items.ctp -->

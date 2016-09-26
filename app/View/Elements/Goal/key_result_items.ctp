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
 * @var                    $can_add_action
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
if (!isset($can_add_action)) {
    $can_add_action = true;
}
if (isset($key_results[key($key_results)]['KeyResult'])) {
    $key_results = Hash::extract($key_results, '{n}.KeyResult');
}
?>

<?= $this->App->viewStartComment()?>
<?php
if ($is_init) {
    $limit_count = MY_GOAL_AREA_FIRST_VIEW_KR_COUNT;
    $count = 1;
}
?>
<?php foreach ($key_results as $kr): ?>
    <li class="dropdown dashboard-goals-card-body-krs">
        <a href="" class="dashboard-goals-card-body-kr-link" data-toggle="dropdown" id="download">
            <hr class="dashboard-goals-card-horizontal-line">
            <i class="fa-key fa <?= $kr['completed'] ? "dashboard-goals-card-body-krs-icon-achieved" : "dashboard-goals-card-body-krs-icon-unachieved" ?>"></i>

            <p class="<?= $kr['completed'] ? "dashboard-goals-card-body-krs-title-achieved" : "dashboard-goals-card-body-krs-title-unachieved" ?>"><?= h($kr['name']) ?></p>
        </a>
        <?= $this->element('Goal/key_result_edit_menu_dropdown',
            ['kr' => $kr, 'without_dropdown_link' => true, 'incomplete_kr_count' => $incomplete_kr_count]) ?>
        <?php if (!$kr['completed'] && $kr_can_edit && $can_add_action): ?>
            <a href="<?= $this->Html->url(['controller'    => 'goals',
                                           'action'        => 'add_action',
                                           'key_result_id' => $kr['id'],
                                           'goal_id'       => $kr['goal_id']
            ]) ?>"
               class="kr_achieve_button fa-check fa dashboard-goals-card-body-krs-action"></a>
        <?php endif; ?>

        <div class="dashboard-goals-card-body-krs-aside">
            <a href="<?= $this->Html->url(['controller'    => 'goals',
                                           'action'        => 'view_actions',
                                           'goal_id'       => $goal_id,
                                           'key_result_id' => $kr['id'],
                                           'page_type'     => 'list'
            ]) ?>"
               class="dashboard-goals-card-body-add-krs-past-action">
                <i class="fa-check fa dashboard-goals-card-body-krs-past-action-icon"></i>
                <span
                    class="dashboard-goals-card-body-krs-past-action-number"><?= h($kr['action_result_count']) ?></span>
            </a>
            <span class="dashboard-goals-card-body-krs-date">
            <?= __('Due.') ?>
            <?= $this->TimeEx->dateNoYear($kr['end_date']) ?>
            </span>
        </div>
    </li>

    <?php
    if ($is_init && $count++ == $limit_count) {
        break;
    }
    ?>
<?php endforeach ?>
<?= $this->App->viewEndComment()?>

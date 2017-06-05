<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 9/26/14
 * Time: 11:14 AM
 *
 * @var CodeCompletionView $this
 * @var                    $goal
 */
?>
<?= $this->App->viewStartComment() ?>
<div class="dropdown pull-right">
    <a href="#"
       class="bd-radius_4px font_lightGray-gray"
       data-toggle="dropdown"
       id="download">
        <i class="fa fa-ellipsis-h profile-goals-function-icon"></i>
    </a>
    <ul class="dropdown-menu dropdown-menu-right frame-arrow-icon" role="menu"
        aria-labelledby="dropdownMenu1">
        <li role="presentation">
            <a href="#"
               data-url="<?= $this->Html->url([
                   'controller' => 'goals',
                   'action'     => 'ajax_get_add_key_result_modal',
                   'goal_id'    => $goal['Goal']['id']
               ]) ?>"
               class="modal-ajax-get-add-key-result"
            ><i class="fa fa-plus-circle"></i><span class="ml_2px">
                                    <?= __("Add Key Result") ?></span></a>
        </li>
        <?php if (!Hash::get($goal, 'Evaluation') && $isAfterCurrentTerm): ?>
            <li role="presentation">
                <a role="menuitem" tabindex="-1"
                   href="/goals/<?= $goal['Goal']['id'] ?>/edit">
                    <i class="fa fa-pencil"></i><span class="ml_2px"><?= __("Edit goal") ?></span>
                </a>
            </li>
            <?php if ($goal['Goal']['can_exchange_tkr']): ?>
                <li role="presentation">
                    <a role="menuitem" tabindex="-1"
                       class="modal-ajax-get-exchange-tkr"
                       href="#"
                       data-url="<?= $this->Html->url([
                           'controller' => 'goals',
                           'action'     => 'ajax_get_exchange_tkr_modal',
                           'goal_id'    => $goal['Goal']['id']
                       ]) ?>">
                        <hr class="dashboard-goals-card-horizontal-line">
                        <i class="fa fa-exchange"></i>
                        <span class="ml_2px"><?= __("Change TKR") ?></span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if ($goal['Goal']['can_change_leader']): ?>
                <li role="presentation">
                    <a role="menuitem" tabindex="-1"
                       class="modal-ajax-get-exchange-leader"
                       href="#"
                       data-url="<?= $this->Html->url([
                           'controller' => 'goals',
                           'action'     => 'ajax_get_exchange_leader_modal',
                           'goal_id'    => $goal['Goal']['id']
                       ]) ?>">
                        <hr class="dashboard-goals-card-horizontal-line">
                        <i class="fa fa-exchange"></i>
                        <span class="ml_2px"><?= __("Change leader") ?></span>
                    </a>
                </li>
            <?php endif; ?>
            <li role="presentation">
                <?=
                $this->Form->postLink('<i class="fa fa-trash"></i><span class="ml_5px">' .
                    __("Delete goal") . '</span>',
                    ['controller' => 'goals', 'action' => 'delete', 'goal_id' => $goal['Goal']['id']],
                    ['escape' => false], __("Do you really want to delete this goal?")) ?>
            </li>
        <?php endif; ?>
        <?php if (in_array($goal['Goal']['id'], $canCompleteGoalIds)): ?>
            <li role="presentation">
                <?=
                $this->Form->postLink('<i class="fa fa-hand-stop-o"></i><span class="ml_5px">' .
                    __("Achieve goal") . '</span>',
                    "/goals/complete/" . $goal['Goal']['id'],
                    ['escape' => false], __("Do you really want to complete this goal?")) ?>
            </li>
        <?php endif; ?>
    </ul>
</div>
<?= $this->App->viewEndComment() ?>

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
            <a href="<?= $this->Html->url([
                'controller' => 'goals',
                'action'     => 'ajax_get_add_key_result_modal',
                'goal_id'    => $goal['Goal']['id']
            ]) ?>"
               class="modal-ajax-get-add-key-result"
            ><i class="fa fa-plus-circle"></i><span class="ml_2px">
                                    <?= __("Add Key Result") ?></span></a>
        </li>
        <?php if (!viaIsSet($goal['Evaluation'])): ?>
            <li role="presentation">
                <a role="menuitem" tabindex="-1"
                   href="/goals/<?= $goal['Goal']['id'] ?>/edit">
                    <i class="fa fa-pencil"></i><span class="ml_2px"><?= __("Edit goal") ?></span>
                </a>
            </li>
            <li role="presentation">
                <?=
                $this->Form->postLink('<i class="fa fa-trash"></i><span class="ml_5px">' .
                    __("Delete goal") . '</span>',
                    ['controller' => 'goals', 'action' => 'delete', 'goal_id' => $goal['Goal']['id']],
                    ['escape' => false], __("Do you really want to delete this goal?")) ?>
            </li>
        <?php endif; ?>
    </ul>
</div>
<?= $this->App->viewEndComment() ?>

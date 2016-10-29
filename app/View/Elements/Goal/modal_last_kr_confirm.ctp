<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/6/14
 * Time: 3:19 PM
 *
 * @var CodeCompletionView $this
 * @var                    $kr_id
 * @var                    $goal
 */
?>
<?= $this->App->viewStartComment()?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true"><span
                    class="close-icon">&times;</span></button>
            <h4 class="modal-title"><?= __("Did you achieve the goal?") ?></h4>
        </div>
        <div class="modal-body modal-circle-body">
            <ul class="add-key-result-goal-info">
                <li>
                    <i class="fa fa-flag"></i><?= __("Goal Name") ?>:<?= h($goal['Goal']['name']) ?>
                </li>
                <li>
                    <?= __("Start date") ?>:<?= $this->Time->format('Y/m/d', $goal['Goal']['start_date']) ?>
                </li>
                <li>
                    <?= __("End date") ?>:<?= $this->Time->format('Y/m/d', $goal['Goal']['end_date']) ?>
                </li>
                <?php if (!empty($goal['Goal']['description'])):?>
                    <li>
                        <?= __("Description") ?>:<?= nl2br($goal['Goal']['description']) ?>
                    </li>
                <?php endif;?>
            </ul>
        </div>
        <div class="modal-footer">
            <div class="text-align_l font_12px font_rouge mb_12px">
                <? __('Whichever you choose, this kr will be finished.') ?>
            </div>
            <?=
            $this->Form->create('Post', [
                'url'           => ['controller'    => 'goals',
                                    'action'        => 'complete_kr',
                                    'key_result_id' => $kr_id,
                                    true
                ],
                'inputDefaults' => [
                    'div'       => 'form-group',
                    'label'     => false,
                    'wrapInput' => '',
                ],
                'class'         => 'form-feed-notify'
            ]); ?>
            <?php $this->Form->unlockField('socket_id') ?>
            <a href="<?= $this->Html->url(['controller'    => 'goals',
                                           'action'        => 'ajax_get_add_key_result_modal',
                                           'goal_id'       => $goal['Goal']['id'],
                                           'key_result_id' => $kr_id
            ]) ?>"
               class="btn btn-default modal-ajax-get-add-key-result" data-dismiss="modal"><?= __(
                    "Add Key Result") ?></a>
            <?=
            $this->Form->submit(__("Achieve the goal"),
                ['class' => 'btn btn-primary', 'div' => false]) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
<?= $this->App->viewEndComment()?>

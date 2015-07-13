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
<!-- START app/View/Elements/Goal/goal_menu_on_my_page.ctp -->
<?php if (!empty($goal['MyCollabo'])): ?>
    <div class="dropdown">
        <?php if ($goal['MyCollabo'][0]['type'] == Collaborator::TYPE_OWNER): ?>
            <a href="#"
               class="pull-right bd-radius_4px font_lightGray-gray"
               data-toggle="dropdown"
               id="download">
                <i class="fa fa-cog goals-column-function-icon"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-right frame-arrow-icon" role="menu"
                aria-labelledby="dropdownMenu1">
                <li role="presentation">
                    <a href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_add_key_result_modal', 'goal_id' => $goal['Goal']['id']]) ?>"
                       class="modal-ajax-get-add-key-result">
                        <i class="fa fa-plus-circle"></i><span class="ml_2px">
                                        <?= __d('gl', "出したい成果を追加") ?></span>
                    </a>
                </li>
                <?php
                $edit_url = ['controller' => 'goals', 'action' => 'add', 'goal_id' => $goal['Goal']['id'], 'mode' => 3];
                $del_url = ['controller' => 'goals', 'action' => 'delete', 'goal_id' => $goal['Goal']['id']];
                ?>
                <?php if (!viaIsSet($goal['Evaluation'])): ?>
                    <li role="presentation"><a role="menuitem" tabindex="-1"
                                               href="<?= $this->Html->url($edit_url) ?>">
                            <i class="fa fa-pencil"></i><span class="ml_2px"><?= __d('gl',
                                                                                     "ゴールを編集") ?></span>
                        </a>
                    </li>
                    <li role="presentation">
                        <?=
                        $this->Form->postLink('<i class="fa fa-trash"></i><span class="ml_5px">' .
                                              __d('gl', "ゴールを削除") . '</span>',
                                              $del_url,
                                              ['escape' => false], __d('gl', "本当にこのゴールを削除しますか？")) ?>
                    </li>
                <?php endif; ?>
            </ul>
        <?php elseif ($goal['MyCollabo'][0]['type'] == Collaborator::TYPE_COLLABORATOR): ?>
            <a href="#"
               class="pull-right bd-radius_4px font_lightGray-gray"
               data-toggle="dropdown"
               id="download">
                <i class="fa fa-cog goals-column-function-icon"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-right frame-arrow-icon" role="menu"
                aria-labelledby="dropdownMenu1">
                <li role="presentation">
                    <a href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_add_key_result_modal', 'goal_id' => $goal['Goal']['id']]) ?>"
                       class="modal-ajax-get-add-key-result"
                        ><i class="fa fa-plus-circle"></i><span class="ml_2px">
                                    <?= __d('gl', "出したい成果を追加") ?></span></a>
                    <a class="modal-ajax-get-collabo"
                       data-toggle="modal"
                       data-target="#ModalCollabo_<?= $goal['Goal']['id'] ?>"
                       href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_collabo_change_modal', 'goal_id' => $goal['Goal']['id']]) ?>">
                        <i class="fa fa-pencil"></i>
                        <span class="ml_2px"><?= __d('gl', "コラボを編集") ?></span>
                    </a>
                </li>
            </ul>
        <?php endif; ?>
    </div>
<?php endif; ?>
<!-- End app/View/Elements/Goal/goal_menu_on_my_page.ctp -->

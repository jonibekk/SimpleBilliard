<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 9/26/14
 * Time: 11:14 AM
 *
 * @var CodeCompletionView $this
 * @var                    $goals
 */
//echo "<pre>";
//print_r($goals);
//exit;
?>
<!-- START app/View/Elements/Goal/index_items.ctp -->
<?php foreach ($goals as $goal): ?>
    <div class="col col-xxs-12 my-goals-item">
        <div class="col col-xxs-3 col-xs-2">
            <a href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_goal_detail_modal', $goal['Goal']['id']]) ?>"
               class="modal-ajax-get">
                <?=
                $this->Html->image('ajax-loader.gif',
                                   [
                                       'class'         => 'lazy img-rounded',
                                       'style'         => 'width: 48px; height: 48px;',
                                       'data-original' => $this->Upload->uploadUrl($goal, 'Goal.photo',
                                                                                   ['style' => 'medium'])
                                   ]
                )
                ?></a>
        </div>
        <div class="col col-xxs-9 col-xs-10 pl_5px">
            <div class="col col-xxs-12 ln_contain">
                <a href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_goal_detail_modal', $goal['Goal']['id']]) ?>"
                   class="modal-ajax-get"><p
                        class="ln_trigger-ff font_verydark"><?= h($goal['Goal']['name']) ?></p></a>
            </div>
            <div class="col col-xxs-12 font_lightgray font_12px">
                <?php if (!empty($goal['Leader'])): ?>
                    <?=
                    __d('gl', "リーダー: %s",
                        h($goal['Leader'][0]['User']['display_username'])) ?>
                <?php endif; ?>
            </div>
            <div class="col col-xxs-12 font_lightgray font_12px">
                <i class="fa fa-check-circle"></i><?= "&nbsp;" . count(($goal['ActionResult'])) . "&nbsp;･&nbsp;" ?>
                <i class="fa fa-key"></i><?= "&nbsp;" . count($goal['KeyResult']) . "&nbsp;･" ?>
                <i class="fa fa-heart"></i><?= "&nbsp;" . count($goal['MyFollow']) . "&nbsp;･" ?>
                <i class="fa fa-child"></i><?php if (count($goal['MyCollabo']) != 0) { ?><?= "&nbsp;" . count($goal['MyCollabo']) . "&nbsp;" ?><?php } ?>
                <?= __d('gl', " ") ?>
                <?php if (count($goal['Collaborator']) == 0): ?>
                    <?= __d('gl', " 0") ?>
                <?php else: ?>
                    <?php $i = 1;
                    foreach ($goal['Collaborator'] as $key => $collaborator): ?>
                        <?php if ($i == 1) {
                            echo "(" . h($collaborator['User']['display_username']);
                        }
                        else {
                            echo h($collaborator['User']['display_username']) . ")";
                        } ?>
                        <?php if (isset($goal['Collaborator'][$key + 1])) {
                            echo ", ";
                        } ?>
                        <?php if ($key == 1) {
                            break;
                        } ?>
                        <?php $i++; endforeach ?>
                    <?php if (($other_count = count($goal['Collaborator']) - 2) > 0): ?>
                        <?= __d('gl', "他%s人", $other_count) ?>
                    <?php endif; ?>
                <?php endif; ?>

            </div>
            <?php if ($goal['Goal']['user_id'] != $this->Session->read('Auth.User.id') && isset($goal['Goal'])): ?>
                <div class="col col-xxs-12 mt_5px">
                    <? $follow_opt = $this->Goal->getFollowOption($goal) ?>
                    <? $collabo_opt = $this->Goal->getCollaboOption($goal) ?>
                    <div class="col col-xxs-6 col-xs-4 mr_5px">
                        <a class="btn btn-white font_verydark bd-circle_22px toggle-follow p_8px <?= $follow_opt['class'] ?>"
                           href="#"
                           data-class="toggle-follow"
                           goal-id="<?= $goal['Goal']['id'] ?>"
                        <?= $follow_opt['disabled'] ?>="<?= $follow_opt['disabled'] ?>">
                        <i class="fa fa-heart font_rougeOrange" style="<?= $follow_opt['style'] ?>"></i>
                        <span class="ml_5px"><?= $follow_opt['text'] ?></span>
                        </a>
                    </div>
                    <div class="col col-xxs-5 col-xs-4">
                        <a class="btn btn-white bd-circle_22px font_verydark modal-ajax-get-collabo p_8px <?= $collabo_opt['class'] ?>"
                           data-toggle="modal"
                           data-target="#ModalCollabo_<?= $goal['Goal']['id'] ?>"
                           href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_collabo_change_modal', $goal['Goal']['id']]) ?>">
                            <i class="fa fa-child font_rougeOrange font_18px" style="<?= $collabo_opt['style'] ?>"></i>
                            <span class="ml_5px font_14px"><?= $collabo_opt['text'] ?></span>
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endforeach ?>
<!-- End app/View/Elements/Goal/index_items.ctp -->

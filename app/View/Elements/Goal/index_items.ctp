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
?>
<!-- START app/View/Elements/Goals/index_items.ctp -->
<? foreach ($goals as $goal): ?>
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
            <div class="col col-xxs-12">
                <a href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_goal_detail_modal', $goal['Goal']['id']]) ?>"
                   class="modal-ajax-get"><b
                        class="line-numbers ln_2 font_verydark"><?= h($goal['SpecialKeyResult'][0]['name']) ?></b></a>
            </div>
            <div class="col col-xxs-12 font_lightgray font_12px">
                <? if (!empty($goal['SpecialKeyResult'][0]['Leader'])): ?>
                    <?=
                    __d('gl', "リーダー: %s",
                        h($goal['SpecialKeyResult'][0]['Leader'][0]['User']['display_username'])) ?>
                <? endif; ?>
                | <?= __d('gl', "コラボ: ") ?>
                <? if (count($goal['SpecialKeyResult'][0]['Collaborator']) == 0): ?>
                    <?= __d('gl', "0人") ?>
                <? else: ?>
                    <? foreach ($goal['SpecialKeyResult'][0]['Collaborator'] as $key => $collaborator): ?>
                        <?= h($collaborator['User']['display_username']) ?>
                        <? if (isset($goal['SpecialKeyResult'][0]['Collaborator'][$key + 1])) {
                            echo ", ";
                        } ?>
                        <? if ($key == 1) {
                            break;
                        } ?>
                    <? endforeach ?>
                    <? if (($other_count = count($goal['SpecialKeyResult'][0]['Collaborator']) - 2) > 0): ?>
                        <?= __d('gl', "他%s人", $other_count) ?>
                    <? endif; ?>
                <?endif; ?>
            </div>
            <? if ($goal['Goal']['user_id'] != $this->Session->read('Auth.User.id') && isset($goal['SpecialKeyResult'][0])): ?>
                <div class="col col-xxs-12 mt_5px">
                    <div class="col col-xxs-4">
                        <? if (empty($goal['SpecialKeyResult'][0]['MyFollow'])) {
                            $follow_class = null;
                            $follow_style = null;
                            $follow_text = __d('gl', "フォロー");
                        }
                        else {
                            $follow_class = 'followed';
                            $follow_style = 'display:none;';
                            $follow_text = __d('gl', "フォロー中");
                        }?>
                        <a class="btn btn-purewhite bd-circle_20 toggle-follow <?= $follow_class ?>" href="#"
                           kr-id="<?= $goal['SpecialKeyResult'][0]['id'] ?>">
                            <i class="fa fa-heart font_rougeOrange" style="<?= $follow_style ?>"></i>
                            <span style="color: #000000" class="ml_5px"><?= $follow_text ?></span>
                        </a>
                    </div>
                    <div class="col col-xxs-4">
                        <? if (isset($goal['SpecialKeyResult'][0]['MyCollabo']) && !empty($goal['SpecialKeyResult'][0]['MyCollabo'])) {
                            $collabo_class = 'collaborated';
                            $collabo_style = 'display:none;';
                            $collabo_text = __d('gl', "コラボり中");
                        }
                        else {
                            $collabo_class = null;
                            $collabo_style = null;
                            $collabo_text = __d('gl', "コラボる");
                        }?>
                        <a class="btn btn-purewhite bd-circle_20 <?= $collabo_class ?>" data-toggle="modal"
                           data-target="#ModalCollabo_<?= $goal['SpecialKeyResult'][0]['id'] ?>" href="#">
                            <i class="fa fa-child font_rougeOrange font_18px" style="<?= $collabo_style ?>"></i>
                            <span style="color: #000000" class="ml_5px font_14px"><?= $collabo_text ?></span>
                        </a>
                    </div>
                </div>
            <? endif; ?>
        </div>
    </div>
    <? if (isset($goal['SpecialKeyResult'][0]) && !empty($goal['SpecialKeyResult'][0])): ?>
        <? $this->append('modal') ?>
        <?= $this->element('modal_collabo', ['skr' => $goal['SpecialKeyResult'][0]]) ?>
        <? $this->end() ?>
        <? $this->append('script') ?>
        <script type="text/javascript">
            $(document).ready(function () {
                $('#CollaboForm_<?=$goal['SpecialKeyResult'][0]['id']?>').bootstrapValidator({
                    live: 'enabled',
                    feedbackIcons: {}
                });
            });
        </script>
        <? $this->end() ?>
    <? endif; ?>
<? endforeach ?>
<!-- End app/View/Elements/Goals/index_items.ctp -->
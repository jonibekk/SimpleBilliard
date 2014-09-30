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
<!-- START app/View/Goals/index_items.ctp -->
<? foreach ($goals as $goal): ?>
    <div class="col col-xxs-12 my-goals-item">
        <div class="col col-xxs-2">
            <?=
            $this->Html->image('ajax-loader.gif',
                               [
                                   'class'         => 'lazy img-rounded',
                                   'style'         => 'width: 48px; height: 48px;',
                                   'data-original' => $this->Upload->uploadUrl($goal, 'Goal.photo',
                                                                               ['style' => 'medium'])
                               ]
            )
            ?>
        </div>
        <div class="col col-xxs-10">
            <div class="col col-xxs-12">
                <a href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_goal_detail_modal', $goal['Goal']['id']]) ?>"
                   class="modal-ajax-get pull-right"><?= __d('gl', "詳細を見る") ?></a>
                <? if (empty($goal['SpecialKeyResult'])): ?>
                    <?= __d('gl', "ゴール未設定") ?>
                <? else: ?>
                    <b class="line-numbers ln_2 font_verydark"><?= h($goal['SpecialKeyResult'][0]['name']) ?></b>
                <?endif; ?>
            </div>
            <? if (!empty($goal['SpecialKeyResult'])): ?>
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
            <? endif; ?>
            <? if ($goal['Goal']['user_id'] != $this->Session->read('Auth.User.id')): ?>
                <div class="col col-xxs-12 mt_5px">
                    <div class="col col-xxs-4">
                        <a class="btn btn-purewhite bd-circle_20 develop--forbiddenLink" href="#"><i class="fa fa-heart font_rougeOrange"><span
                                    style="color: #000000" class="ml_5px"><?= __d('gl', "フォロー") ?></span></i></a>
                    </div>
                    <div class="col col-xxs-4">
                        <a class="btn btn-purewhite bd-circle_20 develop--forbiddenLink" href="#"><i class="fa fa-child font_rougeOrange font_18px"><span
                                    style="color: #000000" class="ml_5px font_14px"><?= __d('gl', "コラボる") ?></span></i></a>
                    </div>
                </div>
            <? endif; ?>
        </div>
    </div>
<? endforeach ?>
<!-- End app/View/Goals/index_items.ctp -->
<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 7/9/15
 * Time: 3:33 PM
 *
 * @var CodeCompletionView $this
 * @var                    $goals
 * @var                    $display_action_count
 * @var                    $is_mine
 */
?>
<!-- START app/View/Users/view_goals.ctp -->
<div class="col-sm-8 col-sm-offset-2">
    <div class="panel panel-default">
        <?= $this->element('simplex_top_section') ?>
        <div class="panel-body">
            <?php foreach ($goals as $goal): ?>
                <div class="col col-xxs-12 my-goals-item">
                    <?php if ($is_mine): ?>
                        <?= $this->element('Goal/goal_menu_on_my_page', ['goal' => $goal]) ?>
                    <?php endif; ?>
                    <div class="col col-xxs-3 col-xs-2">
                        <a href="#">
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
                        <div class="col col-xxs-12 goals-page-card-title-wrapper">
                            <a href="#" class="goals-page-card-title">
                                <p class="font_verydark goals-page-card-title-text">
                                    <span><?= h($goal['Goal']['name']) ?></span>
                                </p>
                            </a>
                        </div>
                        <div class="col col-xxs-12 font_lightgray font_12px">
                            <?= __d('gl', "目的: %s", $goal['Purpose']['name']) ?>
                        </div>
                        <div class="col col-xxs-12 font_lightgray font_12px">
                            <?= __d('gl', "認定ステータス: %s",
                                    Collaborator::$STATUS[$goal['Collaborator'][0]['valued_flg']]) ?>
                        </div>
                        <?php if ($is_mine): ?>
                            <div class="col col-xxs-6 col-xs-4 mr_5px">
                                <a class="btn btn-white font_verydark bd-circle_22px p_8px modal-ajax-get-add-key-result add-key-result"
                                   href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_add_key_result_modal', 'goal_id' => $goal['Goal']['id']]) ?>">
                                    <i class="fa fa-key font_rougeOrange"></i>
                                    <span class="ml_5px"><?= __d('gl', "出したい成果を追加") ?></span>
                                </a>
                            </div>
                        <?php endif; ?>
                        <?php if ($goal['Goal']['user_id'] != $this->Session->read('Auth.User.id') && isset($goal['Goal'])): //ゴールのリーダが自分以外の場合に表示?>
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
                                       href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_collabo_change_modal', 'goal_id' => $goal['Goal']['id']]) ?>">
                                        <i class="fa fa-child font_rougeOrange font_18px"
                                           style="<?= $collabo_opt['style'] ?>"></i>
                                        <span class="ml_5px font_14px"><?= $collabo_opt['text'] ?></span>
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="col col-xxs-12 mt_5px">
                            <ul class="list-inline">
                                <?php if ($is_mine): ?>
                                    <li>
                                        <a href="/"><i class="fa fa-plus"></i><?= __d('gl', "アクションを追加") ?></a>
                                    </li>
                                <?php endif; ?>
                                <?php foreach ($goal['ActionResult'] as $key => $action): ?>
                                    <?php
                                    $last_many = false;
                                    //urlはアクション単体ページ
                                    $url = ['controller' => 'posts', 'action' => 'feed', 'post_id' => $action['Post'][0]['id']];//TODO urlはマイページのアクションリストが完成したら差し替え
                                    //最後の場合かつアクション件数合計が表示件数以上の場合
                                    if ($key == count($goal['ActionResult']) - 1 && count($goal['ActionResultCount']) > $display_action_count) {
                                        $last_many = true;
                                        //urlはゴールページの全アクションリスト
                                        $url = ['controller' => 'posts', 'action' => 'feed', 'post_id' => $action['Post'][0]['id']];//TODO urlはマイページのアクションリストが完成したら差し替え
                                    }
                                    ?>
                                    <li>
                                        <a href="<?= $this->Html->url($url) ?>">
                                            <?=
                                            $this->Html->image('ajax-loader.gif',
                                                               [
                                                                   'class'         => 'lazy',
                                                                   'width'         => 48,
                                                                   'height'        => 48,
                                                                   'data-original' => $this->Upload->uploadUrl($action,
                                                                                                               'ActionResult.photo1',
                                                                                                               ['style' => 'x_small']),
                                                               ]
                                            )
                                            ?>
                                            <?php if ($last_many): ?>
                                                <i class="fa fa-plus"></i><?= count($goal['ActionResultCount']) - $display_action_count + 1 ?>
                                            <?php endif; ?>
                                        </a>
                                    </li>
                                <? endforeach ?>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
    </div>
</div>
<!-- END app/View/Users/view_goals.ctp -->

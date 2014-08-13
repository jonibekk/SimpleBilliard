<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 8/7/14
 * Time: 11:36 AM
 *
 * @var CodeCompletionView $this
 * @var array              $me
 * @var array              $my_circles
 */
?>
<!-- START app/View/Elements/circle_list.ctp -->
<div class="layout-sub_padding clearfix">
    <p class="circle_heading">Circles</p>
    <? if (!empty($my_circles)): ?>
        <? foreach ($my_circles as $circle): ?>
            <div class="circle-layout clearfix">
                <div class="circle-link">
                    <a href="<?= $this->Html->url(['controller' => 'posts', 'action' => 'feed', 'circle_id' => $circle['Circle']['id']]) ?>">
                        <div class="circle-icon_box">
                            <?=
                            $this->Upload->uploadImage($circle, 'Circle.photo', ['style' => 'small'],
                                                       ['width' => '16px', 'height' => '16px']) ?>
                        </div>
                        <div class="circle-name_box">
                            <p title="<?= $circle['Circle']['name'] ?>"><?= $circle['Circle']['name'] ?></p>
                        </div>
                    </a>

                    <div class="circle-count_box">
                        <p class="count-value">
                            <? if ($circle['CircleMember']['unread_count'] > 9): ?>
                                9+
                            <? elseif ($circle['CircleMember']['unread_count'] > 0): ?>
                                <?= $circle['CircleMember']['unread_count'] ?>
                            <?endif; ?>
                        </p>
                    </div>
                </div>
                <div class="circle-function_box clearfix">
                    <? if ($circle['CircleMember']['admin_flg']): ?>
                        <a href="<?= $this->Html->url(['controller' => 'circles', 'action' => 'ajax_get_edit_modal', $circle['Circle']['id']]) ?>"
                           class="modal-ajax-get-circle-edit link-gray develop-floatleft"><i
                                class="fa fa-cog circle-function"></i></a>
                    <? endif; ?>
                </div>
            </div>
        <? endforeach ?>
    <? endif; ?>
    <div class="clearfix develop--circle-seek">
        <i class="fa fa-eye circle-function circle-seek-icon"></i><?=
        $this->Html->link(__d('gl', "公開サークルを見る"),
                          ['controller' => 'circles', 'action' => 'ajax_get_public_circles_modal'],
                          ['class' => 'modal-ajax-get-public-circles']) ?>
    </div>
    <div class="clearfix develop--circle-make">
        <i class="fa fa-plus-circle circle-function circle-make-icon"></i><?=
        $this->Html->link(__d('gl', "サークルを作成する"),
                          ['controller' => '#', 'action' => '#'],
                          ['class' => '#']) ?>
    </div>
</div>
<!-- END app/View/Elements/circle_list.ctp -->
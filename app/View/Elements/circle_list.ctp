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
                <a href="<?= $this->Html->url(['controller' => 'pages', 'action' => 'display', 'home', 'circle_id' => $circle['Circle']['id']]) ?>">
                    <div class="circle-icon_box">
                        <?=
                        $this->Upload->uploadImage($circle, 'Circle.photo', ['style' => 'small'],
                                                   ['width' => '16px', 'height' => '16px']) ?>
                    </div>
                    <div class="circle-name_box">
                        <p title="<?= $circle['Circle']['name'] ?>"><?= $circle['Circle']['name'] ?></p>
                    </div>
                </a>

                <div class="circle-function_box">
                    <? if ($circle['CircleMember']['admin_flg']): ?>
                        <a href="<?= $this->Html->url(['controller' => 'circles', 'action' => 'ajax_get_edit_modal', $circle['Circle']['id']]) ?>"
                           class="modal-ajax-get-circle-edit"><i class="fa fa-cog circle-function"></i></a>
                    <? endif; ?>
                </div>
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
        <? endforeach ?>
    <? endif; ?>
</div>
<!-- END app/View/Elements/circle_list.ctp -->
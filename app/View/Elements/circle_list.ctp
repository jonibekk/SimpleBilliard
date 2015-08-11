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
<div class="dashboard-circle-list layout-sub_padding clearfix">
    <div class="dashboard-circle-list-header">
        <p class="dashboard-circle-list-title circle_heading">Circles</p>
    </div>
    <div class="dashboard-circle-list-body" id="jsDashboardCircleListBody">
        <?php if (!empty($my_circles)): ?>
            <?php foreach ($my_circles as $circle): ?>
                <div class="circle-layout clearfix">
                    <a class="circle-link"
                       href="<?= $this->Html->url(['controller' => 'posts', 'action' => 'feed', 'circle_id' => $circle['Circle']['id']]) ?>">
                        <div class="circle-icon_box">
                            <?=
                            $this->Html->image('ajax-loader.gif',
                                               [
                                                   'class'         => 'lazy',
                                                   'data-original' => $this->Upload->uploadUrl($circle, 'Circle.photo',
                                                                                               ['style' => 'small']),
                                                   'width'         => '16px',
                                                   'height'        => '16px',
                                                   'error-img'     => "/img/no-image-circle.jpg",
                                               ]
                            )
                            ?>
                        </div>
                        <p class="dashboard-circle-name-box circle-name_box" title="<?= h($circle['Circle']['name']) ?>"><?= h($circle['Circle']['name']) ?></p>
                        <span class="dashboard-circle-count-box circle-count_box count-value">
                            <?php if ($circle['CircleMember']['unread_count'] > 9): ?>
                                9+
                            <?php elseif ($circle['CircleMember']['unread_count'] > 0): ?>
                                <?= $circle['CircleMember']['unread_count'] ?>
                            <?php endif; ?>
                        </span>
                    </a>

                    <div class="circle-function_box clearfix">
                        <?php if ($circle['CircleMember']['admin_flg']): ?>
                            <a href="<?= $this->Html->url(['controller' => 'circles', 'action' => 'ajax_get_edit_modal', 'circle_id' => $circle['Circle']['id']]) ?>"
                               class="modal-ajax-get-circle-edit font_lightGray-gray btn-circle-function"><i
                                    class="fa fa-cog circle-function"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach ?>
        <?php endif; ?>
    </div>
    <div class="dashboard-circle-list-footer">
        <div class="clearfix dashboard-circle-list-seek">
            <i class="fa fa-eye circle-function circle-seek-icon font_brownRed"></i><?=
            $this->Html->link(__d('gl', "公開サークルを見る"),
                              ['controller' => 'circles', 'action' => 'ajax_get_public_circles_modal'],
                              ['class' => 'modal-ajax-get-public-circles font-dimgray']) ?>
        </div>
        <div class="clearfix dashboard-circle-list-make">
            <i class="fa fa-plus-circle circle-function circle-make-icon font_brownRed"></i><a href="#" class="font-dimgray"
                                                                                               data-toggle="modal"
                                                                                               data-target="#modal_add_circle"><?=
                __d('gl',
                    "サークルを作成する") ?></a>
        </div>
    </div>
</div>
<!-- END app/View/Elements/circle_list.ctp -->

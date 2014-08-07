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
    <p class="circle_heading">Circle</p>
    <? if (!empty($my_circles)): ?>
        <? foreach ($my_circles as $circle): ?>
            <div class="circle-layout clearfix">
                <div class="circle-icon_box">
                    <?=
                    $this->Upload->uploadImage($circle, 'Circle.photo', ['style' => 'small'],
                                               ['width' => '16px', 'height' => '16px']) ?>
                </div>
                <div class="circle-name_box">
                    <p title="<?= $circle['Circle']['name'] ?>"><?= $circle['Circle']['name'] ?></p>
                </div>
                <div class="circle-count_box">
                    <p class="count-value"><?= $circle['CircleMember']['unread_count'] ?></p>
                </div>
                <div class="circle-function_box">
                    <? if ($circle['CircleMember']['admin_flg']): ?>
                        <i class="fa fa-cog circle-function develop--forbiddenLink"></i>
                    <? endif; ?>
                </div>
            </div>
        <? endforeach ?>
    <? endif; ?>
</div>
<!-- END app/View/Elements/circle_list.ctp -->
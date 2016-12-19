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
<?= $this->App->viewStartComment()?>
<div class="dashboard-circle-list layout-sub_padding clearfix" id="jsDashboardCircleList">
    <div class="dashboard-circle-list-header">
        <p class="dashboard-circle-list-title circle_heading">Circles</p>
    </div>
    <div class="dashboard-circle-list-body-wrap">
        <div class="dashboard-circle-list-body" id="jsDashboardCircleListBody">
            <?php if (!empty($my_circles)): ?>
                <?php foreach ($my_circles as $circle): ?>
                    <?php $isUnread = ($circle['CircleMember']['unread_count'] > 0); ?>
                    <div class="dashboard-circle-list-row-wrap" circle_id="<?= $circle['Circle']['id'] ?>">
                        <?php if ($circle['CircleMember']['admin_flg']): ?>
                            <a href="<?= $this->Html->url([
                                'controller' => 'circles',
                                'action'     => 'ajax_get_edit_modal',
                                'circle_id'  => $circle['Circle']['id']
                            ]) ?>"
                               class="dashboard-circle-list-edit-wrap modal-ajax-get-circle-edit">
                                <i class="fa fa-cog dashboard-circle-list-edit"></i>
                            </a>
                        <?php endif; ?>
                        <a class="dashboard-circle-list-row js-dashboard-circle-list <?= $isUnread ? 'is-unread' : 'is-read' ?>"
                           get-url="<?= $this->Html->url([
                               'controller' => 'posts',
                               'action'     => 'feed',
                               'circle_id'  => $circle['Circle']['id']
                           ]) ?>"
                           image-url="<?= $this->Upload->uploadUrl($circle, 'Circle.photo', ['style' => 'small']) ?>"
                           title="<?= h($circle['Circle']['name']) ?>"
                           circle-id="<?= $circle['Circle']['id'] ?>"
                           public-flg="<?= $circle['Circle']['public_flg'] ?>"
                           team-all-flg="<?= $circle['Circle']['team_all_flg'] ?>"
                           oldest-post-time="<?= $circle['Circle']['created'] ?>"
                           href="#">
                            <div class="dashboard-circle-unread-point">
                                <i class="fa fa-circle" aria-hidden="true"></i>
                            </div>
                            <p class="dashboard-circle-name-box"
                               title="<?= h($circle['Circle']['name']) ?>"><?= h($circle['Circle']['name']) ?>
                            </p>
                            <div class="dashboard-circle-count-box js-circle-count-box">
                                <?php if ($isUnread): ?>
                                    <?php if ($circle['CircleMember']['unread_count'] > 9): ?>
                                        9+
                                    <?php elseif ($circle['CircleMember']['unread_count'] > 0): ?>
                                        <?= $circle['CircleMember']['unread_count'] ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </a>
                    </div>
                <?php endforeach ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="dashboard-circle-list-footer">
        <div class="clearfix dashboard-circle-list-seek">
            <i class="fa fa-eye circle-function circle-seek-icon font_brownRed"></i><?=
            $this->Html->link(__("View Circles"),
                ['controller' => 'circles', 'action' => 'ajax_get_public_circles_modal'],
                ['class' => 'modal-ajax-get-public-circles font-dimgray']) ?>
        </div>
        <div class="clearfix dashboard-circle-list-make">
            <i class="fa fa-plus-circle circle-function circle-make-icon font_brownRed"></i><a href="#"
                                                                                               class="font-dimgray"
                                                                                               data-toggle="modal"
                                                                                               data-target="#modal_add_circle"><?=
                __(
                    "Create a circle") ?></a>
        </div>
    </div>
</div>
<?= $this->App->viewEndComment()?>

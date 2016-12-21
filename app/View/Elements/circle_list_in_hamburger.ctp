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
 * @var                    $is_mb_app
 */
?>
<?= $this->App->viewStartComment()?>
<p class="circle_heading is-humberger"><?= __("Circles") ?></p>
<div class="layout-sub_padding clearfix layout-circle-humbarger js-dashboard-circle-list-body">
    <?php if (!empty($my_circles)): ?>
        <?php foreach ($my_circles as $circle): ?>
            <?php $isUnread = ($circle['CircleMember']['unread_count'] > 0); ?>
            <div class="circle-layout clearfix circleListMore <?= $is_mb_app = true ? "mtb_15px" : null ?>">
                <?php if ($circle['CircleMember']['admin_flg']): ?>
                    <a href="<?= $this->Html->url([
                        'controller' => 'circles',
                        'action'     => 'ajax_get_edit_modal',
                        'circle_id'  => $circle['Circle']['id']
                    ]) ?>"
                       class="dashboard-circle-list-edit-wrap modal-ajax-get-circle-edit"><i
                            class="fa fa-cog dashboard-circle-list-edit font_14px"></i></a>
                <?php endif; ?>
                <a class="dashboard-circle-list-row js-dashboard-circle-list circle-link <?= $isUnread ? 'is-unread' : 'is-read' ?>"
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
                        <div class="circle"></div>
                    </div>
                    <p class="dashboard-circle-name-box"
                       title="<?= h($circle['Circle']['name']) ?>"><?= h($circle['Circle']['name']) ?>
                    </p>
                    <div class="dashboard-circle-count-box-wrapper">
                        <div class="dashboard-circle-count-box js-circle-count-box">
                            <?php if ($isUnread): ?>
                                <?php $unreadCount = $circle['CircleMember']['unread_count']; ?>
                                <?php if ($unreadCount > 0): ?>
                                    <?= $this->NumberEx->addPlusIfOverLimit($unreadCount, $limit = 9); ?>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach ?>
        <?php if (count($my_circles) > 8): ?>
            <div class="circle-view-all-block">
                <i class="fa fa-angle-double-down circle-toggle-icon"></i><a
                    class="pl_5px font_12px font_gray click-circle-trigger on"><?= __("View All") ?></a>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    <div class="clearfix develop--circle-seek <?= $is_mb_app ? "mtb_15px" : null ?>">
        <i class="fa fa-eye circle-function circle-seek-icon font_14px"></i><?=
        $this->Html->link(__("View Circles"),
            ['controller' => 'circles', 'action' => 'ajax_get_public_circles_modal'],
            ['class' => 'modal-ajax-get-public-circles']) ?>
    </div>
    <div class="clearfix develop--circle-make <?= $is_mb_app ? "mtb_15px" : null ?>">
        <i class="fa fa-plus-circle circle-function circle-make-icon font_14px"></i><a href="#" data-toggle="modal"
                                                                                       data-target="#modal_add_circle"><?=
            __(
                "Create a circle") ?></a>
    </div>
</div>
<?= $this->App->viewEndComment()?>

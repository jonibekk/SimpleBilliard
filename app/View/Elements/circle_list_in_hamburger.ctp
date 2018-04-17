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
<?= $this->App->viewStartComment() ?>
<p class="circle_heading is-humberger"><?= __("Circles") ?>
    <a href="/circle_pins/index" class="pull-right pin-circle-edit-color circle-edit-link"><?= __("Manage") ?></a>
</p>
<div class="layout-sub_padding clearfix layout-circle-hamburger js-dashboard-circle-list-body">
    <ul id="circleListHamburger" class="layout-circle-hamburger-body">
        <?= $this->element('Circle/dashboard_list', ['circles' => $my_circles, 'defaultCircle' => $defaultCircle, 'isHamburger' => true]) ?>
    </ul>
</div>
<div class="circle-list-footer">
    <div id="showMoreCirclesToggle" class="clearfix dashboard-circle-list-show-more mtb_15px pin-circle-edit-color">
        <i class="fa fa-chevron-up circle-function circle-show-icon"></i>
        <a href="#" class="circle-view-all font-dimgray show-inline-block"><?=
            __(
                "View All") ?>
        </a>
    </div>
    <div class="clearfix develop--circle-seek mtb_15px pin-circle-edit-color">
        <i class="fa fa-eye circle-function circle-seek-icon font_14px"></i>
        <a href="#" 
            data-url="<?= $this->Html->url([
                'controller' => 'circles',
                'action' => 'ajax_get_public_circles_modal'
            ]) ?>"
            class="font-dimgray modal-ajax-get-public-circles"
        >
            <?= __("View Circles") ?>
        </a>
    </div>
    <div class="clearfix develop--circle-make pin-circle-edit-color">
        <i class="fa fa-plus-circle circle-function circle-make-icon font_14px"></i>
        <a href="#" class="font-dimgray" data-toggle="modal" data-target="#modal_add_circle">
            <?= __("Create a circle") ?>
        </a>
    </div>
</div>
<?= $this->App->viewEndComment() ?>

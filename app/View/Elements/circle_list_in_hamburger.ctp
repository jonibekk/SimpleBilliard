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
<div id="filter-circles-list-hamburger" class="layout-sub_padding clearfix layout-circle-hamburger js-dashboard-circle-list-body">
        <ul>
        <?= $this->element('Circle/dashboard_list', ['circles' => $my_circles, 'defaultCircle' => $defaultCircle, 'isHamburger' => true]) ?>
        <?php if (count($my_circles) > 8): ?>
            <div class="circle-view-all-block">
                <i class="fa fa-angle-double-down circle-toggle-icon"></i>
                <a class="pl_5px font_12px font_gray click-circle-trigger on"><?= __("View All") ?></a>
            </div>
        <?php endif; ?>
        </ul>
    <div class="circle-list-footer">
        <div class="clearfix develop--circle-seek mtb_15px">
            <i class="fa fa-eye circle-function circle-seek-icon font_14px"></i>
            <a href="#" 
                data-url="<?= $this->Html->url([
                    'controller' => 'circles',
                    'action' => 'ajax_get_public_circles_modal'
                ]) ?>"
                class="modal-ajax-get-public-circles"
            >
                <?= __("View Circles") ?>
            </a>
        </div>
        <div class="clearfix develop--circle-make">
            <i class="fa fa-plus-circle circle-function circle-make-icon font_14px"></i>
            <a href="#" data-toggle="modal" data-target="#modal_add_circle">
                <?= __("Create a circle") ?>
            </a>
        </div>
    </div>
</div>
<?= $this->App->viewEndComment() ?>

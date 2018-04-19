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
<?= $this->App->viewStartComment() ?>
<div class="dashboard-circle-list layout-sub_padding clearfix">
    <div class="dashboard-circle-list-header">
        <p class="dashboard-circle-list-title circle_heading"><?= __("Circles") ?>
          <a href="#" class="font-dimgray" data-toggle="modal" data-target="#modal_add_circle">
            <i class="fa fa-plus-circle circle-function circle-make-icon font_brownRed"></i>  
          </a>                                                                   
          <a href="/circle_pins/index" class="pull-right pin-circle-edit-color circle-edit-link"><?= __("Manage") ?></a>
        </p>
    </div>
    <div class="dashboard-circle-list-body-wrap">
        <div id="circleListBody" class="dashboard-circle-list-body js-dashboard-circle-list-body">
          <?= $this->element('Circle/dashboard_list', ['circles' => $my_circles, 'defaultCircle' => $defaultCircle, 'isHamburger' => false]) ?>
        </div>
    </div>
      <div class="dashboard-circle-list-footer">
        <div id="showMoreCircles" class="clearfix dashboard-circle-list-show-more">
            <i class="fa fa-chevron-up circle-function circle-show-icon font_brownRed"></i><a href="#" class="circle-view-all font-dimgray show-inline-block"><?=
                __(
                    "View All") ?></a>
        </div>
        <div class="clearfix dashboard-circle-list-seek">
            <i class="fa fa-eye circle-function circle-seek-icon font_brownRed"></i>
            <a href="#"
               data-url="<?= $this->Html->url([
                   'controller' => 'circles',
                   'action'     => 'ajax_get_public_circles_modal'
               ]) ?>"
               class="modal-ajax-get-public-circles font-dimgray"
            ><?= __("View Circles") ?></a>
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
<?= $this->App->viewEndComment() ?>

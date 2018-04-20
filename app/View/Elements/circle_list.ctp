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
</div>
<?= $this->App->viewEndComment() ?>

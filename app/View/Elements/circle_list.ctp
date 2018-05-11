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
        <div class="dashboard-circle-list-title circle_heading"><?= __("Circles") ?>
          <div class="pull-right circle-menu-dropdown dropdown">
              <a href="#" data-toggle="dropdown" id="circleMenu" class="circle-ellipsis">
                <i class="fa fa-lg fa-ellipsis-h"></i>
              </a>
              <ul id="circleMenuList" class="profile-user-dropdown-menu mod-profile dropdown-menu"
                  aria-labelledby="circleMenu">
                  <li class="profile-user-dropdown-menu-item">
                      <a href="#"
                         class="font-dimgray"
                         data-toggle="modal"
                         data-target="#modal_add_circle">
                      <?= __("Create a circle") ?></a>
                  </li>
                  <li class="profile-user-dropdown-menu-item">
                      <a href="#"
                         data-url="<?= $this->Html->url([
                             'controller' => 'circles',
                             'action'     => 'ajax_get_public_circles_modal'
                         ]) ?>"
                         class="modal-ajax-get-public-circles font-dimgray js-close-dropdown""
                      ><?= __("Discover more circles") ?></a>
                  </li>
                  <li class="profile-user-dropdown-menu-item">
                      <a href="/circle_pins/index" class="pin-circle-edit-color circle-edit-link js-close-dropdown""><?= __("Reorder circles") ?></a>
                  </li>
              </ul>
          </div>                                  
        </div>
    </div>
    <div class="dashboard-circle-list-body-wrap">
        <div id="circleListBody" class="dashboard-circle-list-body js-dashboard-circle-list-body">
          <?= $this->element('Circle/dashboard_list', ['circles' => $my_circles, 'defaultCircle' => $defaultCircle, 'isHamburger' => false]) ?>
        </div>
    </div>
</div>
<?= $this->App->viewEndComment() ?>

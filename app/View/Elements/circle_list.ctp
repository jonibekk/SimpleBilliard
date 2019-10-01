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
        </p>
    </div>
    <div class="dashboard-circle-list-body-wrap">
        <div class="dashboard-circle-list-body js-dashboard-circle-list-body">
            <?= $this->element('Circle/dashboard_list', ['circles' => $my_circles, 'defaultCircle' => $defaultCircle, 'isHamburger' => false]) ?>
        </div>
    </div>
</div>
<?= $this->App->viewEndComment() ?>

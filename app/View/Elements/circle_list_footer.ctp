<?= $this->App->viewStartComment() ?>
<div id="circleListFooter">
<div id="showMoreCircles" class="clearfix dashboard-circle-list-footer dashboard-circle-list-show-more">
    <i class="fa fa-angle-double-down circle-function circle-show-icon font_brownRed"></i><a href="#" class="circle-view-all font_brownRed show-inline-block"><?=
        __(
            "View All") ?></a>
</div>
<div class="clearfix dashboard-circle-list-footer dashboard-circle-list-seek">
    <i class="fa fa-eye circle-function circle-seek-icon font_brownRed"></i>
    <a href="#"
       data-url="<?= $this->Html->url([
           'controller' => 'circles',
           'action'     => 'ajax_get_public_circles_modal'
       ]) ?>"
       class="modal-ajax-get-public-circles font-dimgray"
    ><?= __("View Circles") ?></a>
</div>
<div class="clearfix dashboard-circle-list-footer dashboard-circle-list-make">
    <i class="fa fa-plus-circle circle-function circle-make-icon font_brownRed"></i><a href="#"
                                                                                       class="font-dimgray"
                                                                                       data-toggle="modal"
                                                                                       data-target="#modal_add_circle"><?=
        __(
            "Create a circle") ?></a>
</div>
</div>
<?= $this->App->viewEndComment() ?>
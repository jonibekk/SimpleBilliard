<?= $this->App->viewStartComment()?>
<?php echo $this->Html->Css('fontawesome-all.min'); ?>
<div id="circles-edit-page" class="panel panel-default col-sm-8 col-sm-offset-2 clearfix pin-circle-list">
    <div class="panel-heading">
        <?= __("Edit Circles") ?>
    </div>
    <!-- Todo Filter Commented out/ -->
    <!-- <div class="list-group-item justify-content-between">
      <input type="text" id="filter-circles-list" class="form-control" placeholder="サークルフィルター">
    </div> -->
<!--     <div class="panel-body eval-view-panel-body"> -->
    <div id="pin-circle-panel" class="panel-body pin-circle-view-panel-body">
        <div class="row">
            <div class="column">
                <div class="pin-circle-panel">
                    <div id="pinned-header" class="pin-circle-panel-heading">
                        <div class="alighn-center"><label class="circle-header-label"><?= __("Pinned") ?> </label>  <label id='pinnedCount' class="circle-header-label"></label></div>
                    </div>
                    <div id="pinned-body" class="pin-circle-body">
                        <div class="pin-circle-list-item ignore-elements">
                            <i class="fas fa-align-justify style-hidden pin-circle-front-drag-handler"></i>
                                <?=
                                $this->Html->image('pre-load.svg',
                                    [
                                        'class'         => 'pin-circle-avatar lazy media-object',
                                        'data-original' => $defaultCircle['image'],
                                        'width'         => '32',
                                        'height'        => '32',
                                        'error-img'     => "/img/no-image-link.png",
                                    ]
                                )
                                ?>
                                <div class="pin-circle-text"><label class="circle-name-label"><?php echo $defaultCircle['name'];?></label></div>
                                <i class="fas fa-ellipsis-h fa-lg a-black-link style-hidden"></i>
                                <div class="pin-circle-dropdown-content style-hidden">
                                    <div class="pin-circle-dropdown-element ajax-url style-hidden" data-url=""></div>
                                </div>                                
                                <i class="fa-pull-right-less fas fa-thumbtack fa-lg style-hidden"></i>
                        </div>
                        <ul id="pinned" class="list-group">
                            <?php foreach ($circles as $circle): ?>
                                <?php if(isset($circle['order'])): ?>
                                      <?= $this->element('CirclePins/pinned_element', ['circle' => $circle]) ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <div class="pin-circle-panel">
                    <div id="unpinned-header" class="pin-circle-panel-heading">
                        <div class="alighn-center"><label class="circle-header-label"><?= __("Unpinned") ?></label>  <label id='unpinnedCount' class="circle-header-label"></label></div>
                    </div>
                    <div id="unpinned-body" class="pin-circle-body">
                        <ul id="unpinned" class="list-group">
                            <?php foreach ($circles as $circle): ?>
                                <?php if(!isset($circle['order'])): ?>
                                    <?= $this->element('CirclePins/unpinned_element', ['circle' => $circle]) ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo $this->Html->script('/js/goalous_circle.min'); ?>
<?= $this->App->viewEndComment()?>
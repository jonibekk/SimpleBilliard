<?= $this->App->viewStartComment()?>
<link href="https://use.fontawesome.com/releases/v5.0.8/css/all.css" rel="stylesheet">
<div id="circles-edit-page" class="panel panel-default col-sm-8 col-sm-offset-2 clearfix pin-circle-list">
    <div class="panel-heading">
        <?= __("PinCircle") ?>
    </div>
    <!-- Todo Filter Commented out/ -->
    <!-- <div class="list-group-item justify-content-between">
      <input type="text" id="filter-circles-list" class="form-control" placeholder="サークルフィルター">
    </div> -->
<!--     <div class="panel-body eval-view-panel-body"> -->
    <div class="panel-body pin-circle-view-panel-body">
        <div class="row">
            <div class="column">
                <div class="panel panel-primary">
                    <div id="pinned-header" class="panel panel-heading accordion-toggle">
                        <div class="alighn-center"><label>Pinned </label><label id='pinnedCount'></label></div>
                         <i id="pinned-header-icon" class="pull-right fas fa-caret-up fa-2x fa-custom-caret-position" data-toggle="collapse" data-target="#pinned-body"></i>
                    </div>
                    <div id="pinned-body" class="accordion-body collapse">
                        <div class="list-group-item ignore-elements">
                            <i class="fas fa-align-justify style-hidden"></i>
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
                                <div class="pin-circle-text"><label><?php echo $defaultCircle['name'];?></label></div>
                                <span>
                                <i class="fa-pull-right fas fa-cog fa-lg style-hidden"></i>
                                <i class="fa-pull-right fas fa-thumbtack fa-lg style-hidden"></i>
                                </span>
                        </div>
                        <ul id="pinned" class="list-group accordion-toggle">
                            <?php foreach ($circles as $circle): ?>
                                <?php if(isset($circle['order'])): ?>
                                      <?= $this->element('CirclePins/pinned_element', ['circle' => $circle]) ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <div class="panel panel-primary">
                    <div id="unpinned-header" class="panel panel-heading accordion-toggle">
                        <div class="alighn-center"><label>UnPinned </label><label id='unpinnedCount'></label></div>
                        <i id="unpinned-header-icon" class="pull-right fas fa-caret-up fa-2x fa-custom-caret-position" data-toggle="collapse" data-target="#unpinned-body"></i>
                    </div>
                    <div id="unpinned-body" class="accordion-body collapse">
                        <ul id="unpinned" class="list-group accordion-toggle">
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
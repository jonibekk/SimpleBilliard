<?= $this->App->viewStartComment()?>
<div id="circles-edit-page" class="panel panel-default col-sm-8 col-sm-offset-2 clearfix pin-circle-list">
    <div id="circle-pin-heading" class="panel-heading">
        <?= __("Edit Circles") ?>
        <div class="circle-pin-heading-description">
            <?= __("Description line 1.........") ?>
        </div>
        <div class="circle-pin-heading-description">
            <?= __("Description line 2.........") ?>
        </div>
    </div>
    <div id="pin-circle-panel" class="panel-body pin-circle-view-panel-body">
        <div class="row">
            <div class="column">
                <div class="pin-circle-panel">
                    <div id="pinned-header" class="pin-circle-panel-heading">
                        <div class="alighn-center"><label class="circle-header-label"><?= __("Pinned") ?> </label>  <label id='pinnedCount' class="circle-header-label"></label></div>
                    </div>
                    <div id="pinned-body" class="pin-circle-body">
                        <div class="pin-circle-list-item ignore-elements">
                            <i class="fa fa-align-justify style-hidden pin-circle-front-drag-handler"></i>
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
                                <div class="pin-circle-text"><label class="circle-name-label"><?php echo mb_strimwidth ($defaultCircle['name'] , 0 , 16, '...', 'UTF-8');?></label></div>
                                <?php if ($defaultCircle['admin_flg']): ?>
                                <a href="#" class="fa-pull-right-pin-circle-cog pin-circle-dropdown-element ajax-url" data-url="/circles/ajax_get_edit_modal/circle_id:<?php echo $defaultCircle['id']; ?>"><i class="fa fa-cog fa-lg pin-circle-cog"></i></a>
                                <?php else :?>
                                <a href="#" class="fa-pull-right-pin-circle-cog pin-circle-dropdown-element ajax-url" data-url=""><i class="fa fa-cog fa-lg pin-circle-cog style-hidden"></i></a>
                                <?php endif; ?>                            
                                <i class="fa-pull-right-less fa fa-thumb-tack fa-lg style-hidden"></i>
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
<?= $this->App->viewEndComment()?>
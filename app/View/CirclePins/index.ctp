<?= $this->App->viewStartComment()?>
<div class="panel panel-default col-sm-8 col-sm-offset-2 clearfix pin-circle-list">
    <div class="panel-heading">
        <?= __("PinCircle") ?>
    </div>
    <div class="list-group-item justify-content-between">
      <input type="text" id="filter-circles-list" class="form-control" placeholder="サークルフィルター">
    </div>
<!--     <div class="panel-body eval-view-panel-body"> -->
    <div class="panel-body pin-circle-view-panel-body">
        <div class="row">
            <div class="column">
                <div class="panel panel-primary">
                    <div class="panel-body">
                        <div class="list-group-item ignore-elements">
                                <?=
                                $this->Html->image('pre-load.svg',
                                    [
                                        'class'         => 'pin-circle-avatar lazy media-object',
                                        'data-original' => $defaultCircle[0]['Circle']['image'],
                                        'width'         => '32',
                                        'height'        => '32',
                                        'error-img'     => "/img/no-image-link.png",
                                    ]
                                )
                                ?>
                                <div class="pin-circle-text"><label><?php echo $defaultCircle[0]['Circle']['name'];?></label></div>
                                <?=
                                $this->Html->image('pre-load.svg',
                                    [
                                        'class'         => 'pull-right lazy media-object',
                                        'data-original' => "/img/no-image-link.png",
                                        'width'         => '32',
                                        'height'        => '32',
                                        'error-img'     => "/img/no-image-link.png",
                                    ]
                                )
                                ?>
                        </div>
                        <ul id="pinned" class="list-group">
                            <?php foreach ($pinnedCircles as $circle): ?>
                              <li id="<?= $circle['Circle']['id']?>" class="list-group-item justify-content-between">
                                <?=
                                    $this->Html->image('pre-load.svg',
                                        [
                                            'class'         => 'pin-circle-avatar lazy media-object',
                                            'data-original' => $circle['Circle']['image'],
                                            'width'         => '32',
                                            'height'        => '32',
                                            'error-img'     => "/img/no-image-link.png",
                                        ]
                                    )
                                    ?>
                                <label class='circle-name'><?php echo $circle['Circle']['name'];?></label>
                                <?=
                                    $this->Html->image('pre-load.svg',
                                        [
                                            'class'         => 'pin pull-right lazy media-object',
                                            'data-original' => "/img/npin-image.png",
                                            'width'         => '32',
                                            'height'        => '32',
                                            'error-img'     => "/img/no-image-link.png",
                                        ]
                                    )
                                    ?>
                              </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <div class="panel panel-primary">
                    <div class="panel-body">
                        <ul id="unpinned" class="list-group">
                            <?php foreach ($unpinnedCircles as $circle): ?>
                              <li id="<?= $circle['Circle']['id']?>" class="list-group-item justify-content-between">
                                <?=
                                    $this->Html->image('pre-load.svg',
                                        [
                                            'class'         => 'pin-circle-avatar lazy media-object',
                                            'data-original' => $circle['Circle']['image'],
                                            'width'         => '32',
                                            'height'        => '32',
                                            'error-img'     => "/img/no-image-link.png",
                                        ]
                                    )
                                    ?>
                                <label class='circle-name'><?php echo $circle['Circle']['name'];?></label>
                                <?=
                                    $this->Html->image('pre-load.svg',
                                        [
                                            'class'         => 'unpin pull-right lazy media-object',
                                            'data-original' => "/img/nunpin-image.png",
                                            'width'         => '32',
                                            'height'        => '32',
                                            'error-img'     => "/img/no-image-link.png",
                                        ]
                                    )
                                    ?>
                              </li>
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
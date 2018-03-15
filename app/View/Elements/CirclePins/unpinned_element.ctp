<?= $this->App->viewStartComment()?>
<?php $circleEdit = $this->Html->url([
               'controller' => 'circles',
               'action'     => 'ajax_get_edit_modal',
               'circle_id'  => $circle['id']
           ]) ?>
<li id="<?= $circle['id']?>" class="list-group-item justify-content-between">
	<i class="fas fa-align-justify style-hidden"></i>
	<?=
	    $this->Html->image('pre-load.svg',
	        [
	            'class'         => 'pin-circle-avatar lazy media-object',
	            'data-original' => $circle['image'],
	            'width'         => '32',
	            'height'        => '32',
	            'error-img'     => "/img/no-image-link.png",
	        ]
	    )
	?>
	<label class='circle-name-label'><?php echo $circle['name'];?></label>
	<?php if ($circle['admin_flg']): ?>
        <a href='#'
           data-url='<?php $circleEdit ?>'
           class='a-black-link'>
            <i class='fa-pull-right-less fas fa-ellipsis-h fa-lg style-hidden'></i>
        </a>
    <?php endif; ?>
	<i class="fa-pull-right-less fas fa-thumbtack fa-lg fa-disabled"></i>
</li>
<?= $this->App->viewEndComment()?>
<?= $this->App->viewStartComment()?>
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
	<div class="dropdown fa-pull-right-dropdown">
		<i class="fas fa-ellipsis-h fa-lg a-black-link"></i>
		<div class="dropdown-content">
			<div class="dropdown-element move-top">Move to top</div>
			<div class="dropdown-element move-bottom">Move to bottom</div>
			<a href="#" class="dropdown-elementajax-url" data-url="/circle_pins/ajax_get_edit_modal/circle_id:<?php echo $circle['id']; ?>">Edit</a>
		</div>
	</div>
	<?php else :?>
	<div class="dropdown fa-pull-right-dropdown">
		<i class="fas fa-ellipsis-h fa-lg a-black-link"></i>
		<div class="dropdown-content">
			<div class="dropdown-element move-top">Move to top</div>
			<div class="dropdown-element move-bottom">Move to bottom</div>
		</div>
	</div>
	<?php endif; ?>
	<i class="fa-pull-right-less fas fa-thumbtack fa-lg fa-disabled"></i>
</li>
<?= $this->App->viewEndComment()?>
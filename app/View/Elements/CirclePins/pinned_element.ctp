<?= $this->App->viewStartComment()?>
<li id="<?= $circle['id']?>" class="list-group-item justify-content-between">
	<i class="fas fa-align-justify"></i>
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
	<div class="pin-circle-dropdown fa-pull-right-pin-circle-dropdown">
		<i class="fas fa-ellipsis-h fa-lg a-black-link"></i>
		<div class="pin-circle-dropdown-content">
			<div class="pin-circle-dropdown-element move-top">Pin to top</div>
			<div class="pin-circle-dropdown-element move-bottom">Pin to bottom</div>
			<a href="#" class="pin-circle-dropdown-element ajax-url" data-url="/circle_pins/ajax_get_edit_modal/circle_id:<?php echo $circle['id']; ?>">Edit</a>
		</div>
	</div>
	<?php else :?>
	<div class="pin-circle-dropdown fa-pull-right-pin-circle-dropdown style-hidden">
		<i class="fas fa-ellipsis-h fa-lg a-black-link"></i>
		<div class="pin-circle-dropdown-content">
			<div class="pin-circle-dropdown-element move-top">Pin to top</div>
			<div class="pin-circle-dropdown-element move-bottom">Pin to bottom</div>
		</div>
	</div>
	<?php endif; ?>
	<i class="fa-pull-right-less fas fa-thumbtack fa-lg"></i>
</li>
<?= $this->App->viewEndComment()?>
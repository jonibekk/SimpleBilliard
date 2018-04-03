<?= $this->App->viewStartComment()?>
<li id="<?= $circle['id']?>" class="justify-content-between pin-circle-list-item">
	<i class="fa fa-align-justify fa-lg style-hidden pin-circle-front-drag-handler"></i>
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
	<a href="#" class="fa-pull-right-pin-circle-cog ajax-url" data-url="/circles/ajax_get_edit_modal/circle_id:<?php echo $circle['id']; ?>"><i class="fa fa-cog fa-2x pin-circle-cog"></i></a>
	<?php else :?>
	<a href="#" class="fa-pull-right-pin-circle-cog pin-circle-dropdown-element ajax-url" data-url=""><i class="fa fa-cog fa-2x pin-circle-cog style-hidden"></i></a>
	<?php endif; ?>
	<i class="fa-pull-right-less fa fa-thumb-tack fa-2x fa-disabled a-black-link"></i>
</li>
<?= $this->App->viewEndComment()?>
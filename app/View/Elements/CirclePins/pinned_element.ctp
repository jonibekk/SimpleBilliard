<?= $this->App->viewStartComment()?>
<li id="<?= $circle['id']?>" class="justify-content-between pin-circle-list-item">
	<i class="fa fa-align-justify pin-circle-front-drag-handler"></i>
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
	<label class='circle-name-label'><?php echo mb_strimwidth ($circle['name'] , 0 , 16, '...', 'UTF-8');?></label>
	<?php if ($circle['admin_flg']): ?>
	<a href="#" class="fa-pull-right-pin-circle-cog pin-circle-dropdown-element ajax-url" data-url="/circle_pins/ajax_get_edit_modal/circle_id:<?php echo $circle['id']; ?>"><i class="fa fa-cog fa-lg pin-circle-cog"></i></a>
	<?php else :?>
	<a href="#" class="fa-pull-right-pin-circle-cog pin-circle-dropdown-element ajax-url" data-url=""><i class="fa fa-cog fa-lg pin-circle-cog style-hidden"></i></a>
	<?php endif; ?>
	<i class="fa-pull-right-less fa fa-thumb-tack fa-lg a-black-link"></i>
</li>
<?= $this->App->viewEndComment()?>
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
	<i class="fa-pull-right fas fa-cog fa-lg<?php if(!$circle['admin_flg']){echo ' style-hidden';} ?>"></i>
	<i class="fa-pull-right fas fa-thumbtack fa-lg fa-disabled"></i>
</li>
<?= $this->App->viewEndComment()?>
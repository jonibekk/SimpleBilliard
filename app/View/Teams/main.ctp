<?= $this->App->viewStartComment()?>

<?php echo $this->Html->css('team_page'); ?>

<div ng-app="myApp">
    <div class="col-xs-3">
        <?= $this->element('Team/side_menu', ['angular' => true]) ?>
    </div>

    <div class="col-xs-9">
        <div ui-view> ロード中....</div>
    </div>
</div>

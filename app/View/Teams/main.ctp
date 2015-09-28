<!-- START app/View/Teams/member_list.ctp -->

<?php echo $this->Html->css('team_page'); ?>
<?php echo $this->Html->script('app/team'); ?>
<?php echo $this->Html->script('controller/team_member_list'); ?>
<?php echo $this->Html->script('controller/team_vision_list'); ?>
<?php echo $this->Html->script('controller/group_vision_list'); ?>


<div ng-app="myApp">
    <div class="col-xs-3">
        <?= $this->element('Team/side_menu', ['angular' => true]) ?>
    </div>

    <div class="col-xs-9">
        <div ui-view> ロード中....</div>
    </div>
</div>

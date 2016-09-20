<?php
/**
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 2014/07/19
 * Time: 22:11
 *
 * @var                    $circles
 * @var CodeCompletionView $this
 * @var                    $joined_circle_count_list
 */
?>
<?= $this->App->viewStartComment()?>
<div class="modal-dialog modal-public-circles-dialog">
    <div class="modal-content modal-public-circles-contents">
        <div class="modal-header none-border">
            <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true"><span
                    class="close-icon">&times;</span></button>
            <?php
            $count_joined = count($joined_circles);
            $count_non_joined = count($non_joined_circles);
            $count_all = $count_joined + $count_non_joined;
            ?>
            <h4 class="modal-title"><?= __("Circles") . " (" . $count_all . ")" ?></h4>
        </div>
        <ul class="nav nav-tabs">
            <li class="active"><a href="#tab1"
                                  data-toggle="tab"><?= __("Unjoined") . " (" . $count_non_joined . ")" ?></a></li>
            <li><a href="#tab2" data-toggle="tab"><?= __("Joined") . " (" . $count_joined . ")" ?></a></li>
        </ul>
        <?=
        $this->Form->create('Circle', [
            'url'           => ['controller' => 'circles', 'action' => 'ajax_join_circle'],
            'inputDefaults' => [
                'div'       => false,
                'label'     => [
                    'class' => ''
                ],
                'wrapInput' => false,
                'class'     => ''
            ],
            'class'         => '',
            'novalidate'    => true,
            'id'            => 'CircleJoinForm',
        ]); ?>
        <?= $this->Form->hidden('0.join'); ?>
        <?= $this->Form->hidden('0.circle_id'); ?>
        <?php $this->Form->unlockField('Circle.0.join'); ?>
        <?php $this->Form->unlockField('Circle.0.circle_id'); ?>
        <?= $this->Form->end() ?>
        <div class="modal-body modal-feed-body tab-content">
            <div class="tab-pane fade in active" id="tab1">
                <?php $key = 0 ?>
                <?php if (!empty($non_joined_circles)): ?>
                    <div class="row borderBottom">
                        <?php foreach ($non_joined_circles as $key => $circle): ?>
                            <?= $this->element('public_circle_item',
                                [
                                    'circle'       => $circle,
                                    'key'          => $key,
                                    'admin'        => false,
                                    'joined'       => false,
                                    'member_count' => count($circle['CircleMember']),
                                ]) ?>
                        <?php endforeach ?>
                    </div>
                <?php else: ?>
                    <?= __("You belong to all circles in this team.") ?>
                <?php endif ?>
            </div>
            <div class="tab-pane fade" id="tab2">
                <?php if (!empty($joined_circles)): ?>
                    <div class="row borderBottom">
                        <?php foreach ($joined_circles as $circle): ?>
                            <?php ++$key ?>
                            <?= $this->element('public_circle_item',
                                [
                                    'circle'       => $circle,
                                    'key'          => $key,
                                    'admin'        => $circle['CircleMember']['admin_flg'],
                                    'joined'       => true,
                                    'member_count' => $joined_circle_count_list[$circle['Circle']['id']],
                                ]) ?>
                        <?php endforeach ?>
                    </div>
                <?php else: ?>
                    <?= __("You don't belong to any circle.") ?>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>
<?= $this->App->viewEndComment()?>

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
<!-- START app/View/Elements/modal_public_circles.ctp -->
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header none-border">
            <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true"><span
                    class="close-icon">&times;</span></button>
            <?php
            $count_joined=count($joined_circles);
            $count_non_joined=count($non_joined_circles);
            $count_all=$count_joined+$count_non_joined;
            ?>
            <h4 class="modal-title"><?= __d('gl', "サークル")." (".$count_all.")"?></h4>
        </div>
        <ul class="nav nav-tabs">
            <li class="active"><a href="#tab1" data-toggle="tab"><?= __d('gl', "参加していない")." (".$count_non_joined.")" ?></a></li>
            <li><a href="#tab2" data-toggle="tab"><?= __d('gl', "参加している")." (".$count_joined.")" ?></a></li>
        </ul>
        <?=
        $this->Form->create('Circle', [
            'url'           => ['controller' => 'circles', 'action' => 'join'],
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
        <div class="modal-body modal-feed-body tab-content">
            <div class="tab-pane fade in active" id="tab1">
                <?php $key = 0 ?>
                <?php if (!empty($non_joined_circles)): ?>
                    <div class="row borderBottom">
                        <?php foreach ($non_joined_circles as $key => $circle): ?>
                            <?= $this->element('public_circle_item',
                                               ['circle'       => $circle,
                                                'key'          => $key,
                                                'admin'        => false,
                                                'joined'       => false,
                                                'member_count' => count($circle['CircleMember']),
                                               ]) ?>
                        <?php endforeach ?>
                    </div>
                <?php else: ?>
                    <?= __d('gl', "参加していないサークルはありません。") ?>
                <?php endif ?>
            </div>
            <div class="tab-pane fade" id="tab2">
                <?php if (!empty($joined_circles)): ?>
                    <div class="row borderBottom">
                        <?php foreach ($joined_circles as $circle): ?>
                            <?php ++$key ?>
                            <?= $this->element('public_circle_item',
                                               ['circle'       => $circle,
                                                'key'          => $key,
                                                'admin'        => $circle['CircleMember']['admin_flg'],
                                                'joined'       => true,
                                                'member_count' => $joined_circle_count_list[$circle['Circle']['id']],
                                               ]) ?>
                        <?php endforeach ?>
                    </div>
                <?php else: ?>
                    <?= __d('gl', "参加しているサークルはありません。") ?>
                <?php endif ?>
            </div>
        </div>
        <div class="modal-footer modal-feed-footer">
            <?php if (!empty($joined_circles) || !empty($non_joined_circles)): ?>
                <?=
                $this->Form->submit(__d('gl', "変更を保存"),
                                    ['class' => 'btn btn-primary pull-right', 'div' => false /*, 'disabled' => 'disabled'*/]) ?>
                <button type="button" class="btn btn-link design-cancel mr_8px bd-radius_4px"
                        data-dismiss="modal"><?= __d('gl',
                                                     "キャンセル") ?></button>
            <?php else: ?>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= __d('gl', "閉じる") ?></button>
            <?php endif; ?>
        </div>
        <?= $this->Form->end() ?>
    </div>
</div>
<!-- END app/View/Elements/modal_public_circles.ctp -->

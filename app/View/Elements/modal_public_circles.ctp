<?php
/**
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 2014/07/19
 * Time: 22:11
 *
 * @var                    $circles
 * @var CodeCompletionView $this
 */
?>
<!-- START app/View/Elements/modal_public_circles.ctp -->
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header none-border">
            <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true"><span
                    class="close-icon">&times;</span></button>
            <h4 class="modal-title font_18px font_bold"><?= __d('gl', "公開サークル") ?></h4>
        </div>
        <ul class="nav nav-tabs">
            <li class="active"><a href="#tab1" data-toggle="tab"><?= __d('gl', "参加していない") ?></a></li>
            <li><a href="#tab2" data-toggle="tab"><?= __d('gl', "参加している") ?></a></li>
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
                <? $key = 0 ?>
                <? if (!empty($non_joined_circles)): ?>
                    <div class="row borderBottom">
                        <? foreach ($non_joined_circles as $key => $circle): ?>
                            <?= $this->element('public_circle_item', ['circle' => $circle, 'key' => $key]) ?>
                        <? endforeach ?>
                    </div>
                <? else: ?>
                    <?= __d('gl', "参加していないサークルはありません。") ?>
                <? endif ?>
            </div>
            <div class="tab-pane fade" id="tab2">
                <? if (!empty($joined_circles)): ?>
                    <div class="row borderBottom">
                        <? foreach ($joined_circles as $circle): ?>
                            <? ++$key ?>
                            <?= $this->element('public_circle_item', ['circle' => $circle, 'key' => $key]) ?>
                        <? endforeach ?>
                    </div>
                <? else: ?>
                    <?= __d('gl', "参加しているサークルはありません。") ?>
                <? endif ?>
            </div>
        </div>
        <div class="modal-footer modal-feed-footer">
            <? if (!empty($joined_circles) || !empty($non_joined_circles)): ?>
                <?=
                $this->Form->submit(__d('gl', "変更を保存"),
                                    ['class' => 'btn btn-primary pull-right', 'div' => false /*, 'disabled' => 'disabled'*/]) ?>
                <button type="button" class="btn btn-link design-cancel mr_8px bd-radius_4px"
                        data-dismiss="modal"><?= __d('gl',
                                                     "キャンセル") ?></button>
            <? else: ?>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= __d('gl', "閉じる") ?></button>
            <? endif; ?>
        </div>
        <?= $this->Form->end() ?>
    </div>
</div>
<!-- END app/View/Elements/modal_public_circles.ctp -->

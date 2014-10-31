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
        <div class="modal-header">
            <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true"><span
                    class="close-icon">&times;</span></button>
            <h4 class="modal-title font_18px font_bold"><?= __d('gl', "公開サークル") ?></h4>
        </div>
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
        <div class="modal-body modal-feed-body">
            <? if (!empty($circles)): ?>
                <div class="row borderBottom">
                    <? foreach ($circles as $key => $circle): ?>
                        <?=
                        $this->element('public_circle_item', ['circle' => $circle, 'key' => $key]) ?>
                    <? endforeach ?>
                </div>
            <? else: ?>
                <?= __d('gl', "公開サークルはありません。") ?>
            <?endif ?>
        </div>
        <div class="modal-footer modal-feed-footer">
            <? if (!empty($circles)): ?>
                <?=
                $this->Form->submit(__d('gl', "変更を保存"),
                                    ['class' => 'btn btn-primary pull-right', 'div' => false /*, 'disabled' => 'disabled'*/]) ?>
                <button type="button" class="btn btn-link design-cancel mr_8px bd-radius_4px"
                        data-dismiss="modal"><?= __d('gl',
                                                                                                      "キャンセル") ?></button>
            <? else: ?>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= __d('gl', "閉じる") ?></button>
            <?endif; ?>
        </div>
        <?= $this->Form->end() ?>
    </div>
</div>
<!-- END app/View/Elements/modal_public_circles.ctp -->

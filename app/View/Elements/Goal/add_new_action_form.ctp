<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 1/20/15
 * Time: 6:34 PM
 *
 * @var $goal_id
 * @var $ar_count
 */
?>
<!-- START app/View/Elements/Goal/add_new_action_form.ctp -->
<?= $this->Form->create('ActionResult', [
    'inputDefaults' => [
        'div'       => 'form-group mb_5px develop--font_normal',
        'wrapInput' => false,
        'class'     => 'form-control',
    ],
    'url'           => ['controller' => 'goals', 'action' => 'add_completed_action', $goal_id],
    'type'          => 'file',
]); ?>
<?=
$this->Form->input('ActionResult.name', [
                                          'label'          => false,
                                          'rows'           => 1,
                                          'placeholder'    => __d('gl', "今日やったアクションを共有しよう！"),
                                          'class'          => 'form-control tiny-form-text blank-disable col-xxs-10 goalsCard-actionInput mb_12px add-select-options',
                                          'id'             => "ActionFormName_" . $goal_id,
                                          'target-id'      => "ActionFormSubmit_" . $goal_id,
                                          'select-id'      => "ActionKeyResultId_" . $goal_id,
                                          'add-select-url' => $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_kr_list', $goal_id])
                                      ]
)
?>
<div class="goalsCard-activity inline-block col-xxs-2">
    <? if ($ar_count > 0): ?>
        <a class="click-show-post-modal font_gray-brownRed pointer"
           id="ActionListOpen_<?= $goal_id ?>"
           href="<?= $this->Html->url(['controller' => 'posts', 'action' => 'ajax_get_goal_action_feed', 'goal_id' => $goal_id, 'type' => Post::TYPE_ACTION]) ?>">
            <i class="fa fa-check-circle mr_1px"></i><span
                class="ls_number"><?= $ar_count ?></span>
        </a>
    <? else: ?>
        <i class="fa fa-check-circle mr_1px"></i><span
            class="ls_number">0</span>
    <? endif; ?>
</div>
<div id="ActionFormDetail_<?= $goal_id ?>">
    <div class="form-group">
        <label class="font_normal col-xxs-4 lh_40px" for="ActionPhotos">
            <i class="fa fa-camera mr_2px"></i><?= __d('gl', "画像") ?>
        </label>

        <div class="col-xxs-8">
            <ul class="col input-images post-images">
                <? for ($i = 1; $i <= 5; $i++): ?>
                    <li>
                        <?= $this->element('Feed/photo_upload_mini',
                                           ['type' => 'action_result', 'index' => $i, 'submit_id' => 'PostSubmit', 'has_many' => false]) ?>
                    </li>
                <? endfor ?>
            </ul>
        </div>
    </div>
    <label class="font_normal col-xxs-4 lh_40px" for="KeyResults_<?= $goal_id ?>">
        <i class="fa fa-key mr_2px"></i><?= __d('gl', "成果") ?>
    </label>
    <?=
    $this->Form->input('ActionResult.key_result_id', [
                                                       'label'   => false, //__d('gl', "紐付ける出したい成果を選択(オプション)"),
                                                       'options' => [null => __d('gl', "選択なし")],
                                                       'class'   => 'form-control col-xxs-8 selectKrForAction',
                                                       'id'      => 'ActionKeyResultId_' . $goal_id,
                                                   ]
    )
    ?>
    <div class="form-group col-xxs-12 mt_12px">
        <a href="#" target-id="ActionFormName_<?= $goal_id ?>"
           class="btn btn-white tiny-form-text-close font_verydark"><?= __d('gl',
                                                                            "キャンセル") ?></a>
        <?= $this->Form->submit(__d('gl', "アクション登録"), [
            'div'      => false,
            'id'       => "ActionFormSubmit_" . $goal_id,
            'class'    => 'btn btn-info pull-right',
            'disabled' => 'disabled',
        ]); ?>
    </div>
</div>
<?= $this->Form->end() ?>
<!-- END app/View/Elements/Goal/add_new_action_form.ctp -->

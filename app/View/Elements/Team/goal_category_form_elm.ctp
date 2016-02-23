<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 *
 * @var CodeCompletionView $this
 * @var                    $index
 */
if (!viaIsSet($index) || !is_numeric($index)) {
    $index = 0;
}
?>

<!-- START app/View/Elements/Team/goal_category_form_elm.ctp -->
<tr class="evaluation_select"
    index="<?php echo $index ?>" id="AddScore_<?= $index ?>">
    <td>
        <div class="form-group">

            <?= $this->Form
                ->input("GoalCategory.$index.name",
                        array(
                            'wrapInput'                    => false,
                            'type'                         => 'text',
                            'div'                          => false,
                            'label'                        => false,
                            'class'                        => 'form-control',
                            'placeholder'                  => __('名前'),
                            'data-bv-notempty-message'     => __("入力必須項目です。"),
                            'data-bv-stringlength'         => "true",
                            'data-bv-stringlength-max'     => 200,
                            'data-bv-stringlength-message' => __('最大文字数(%s)を超えています。', 200),
                            'required'                     => true,
                        ))
            ?>
        </div>
    </td>
    <td>
        <div class="form-group">

            <?= $this->Form
                ->input("GoalCategory.$index.description",
                        array(
                            'wrapInput'                    => false,
                            'type'                         => 'textarea',
                            'rows'                         => 3,
                            'div'                          => false,
                            'label'                        => false,
                            'class'                        => 'form-control',
                            'placeholder'                  => __('ゴールカテゴリの説明を書きましょう'),
                            'data-bv-notempty-message'     => __("入力必須項目です。"),
                            'required'                     => true,
                            'data-bv-stringlength'         => 'true',
                            'data-bv-stringlength-max'     => 2000,
                            'data-bv-stringlength-message' => __("最大文字数(%s)を超えています。", 2000),
                        ))
            ?>
        </div>
    </td>
    <?php if (viaIsSet($type) && viaIsSet($id) && $type == 'exists'): ?>
        <td>
            <a class="modal-ajax-get"
               href="<?= $this->Html->url(['controller' => 'teams', 'action' => 'ajax_get_confirm_inactive_goal_category_modal', 'team_id' => $id]) ?>"><i
                    class="fa fa-trash font_lightGray-gray"></i></a>
        </td>
        <?= $this->Form->hidden("GoalCategory.$index.id") ?>
    <?php else: ?>
        <td>
            <a href="#" class="click-target-remove" target-selector="#AddScore_<?= $index ?>"><i
                    class="fa fa-times font_lightGray-gray"></i></a>
        </td>
    <?php endif; ?>
</tr>
<!-- END app/View/Elements/Team/goal_category_form_elm.ctp -->

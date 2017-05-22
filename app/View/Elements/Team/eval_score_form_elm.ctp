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

<?= $this->App->viewStartComment() ?>
<tr class="evaluation_select"
    index="<?php echo $index ?>" id="AddScore_<?= $index ?>">
    <td>
        <div class="form-group">

            <?= $this->Form
                ->input("EvaluateScore.$index.name",
                    array(
                        'wrapInput'                    => false,
                        'type'                         => 'text',
                        'div'                          => false,
                        'label'                        => false,
                        'class'                        => 'form-control',
                        'placeholder'                  => __('eg. A-E or 1-5'),
                        'data-bv-notempty-message'     => __("Input is required."),
                        'required'                     => true,
                        'data-bv-stringlength'         => 'true',
                        'data-bv-stringlength-max'     => 200,
                        'data-bv-stringlength-message' => __("It's over limit characters (%s).", 200),
                    ))
            ?>
        </div>
    </td>
    <td>
        <div class="form-group">

            <?= $this->Form
                ->input("EvaluateScore.$index.index_num",
                    array(
                        'wrapInput'                    => false,
                        'type'                         => 'number',
                        'div'                          => false,
                        'label'                        => false,
                        'class'                        => 'form-control',
                        'placeholder'                  => __('eg. 1 (Numeric)'),
                        'data-bv-notempty-message'     => __("Input is required"),
                        'data-bv-integer-message'      => __("Please enter a number."),
                        'required'                     => true,
                        'data-bv-stringlength'         => 'true',
                        'data-bv-stringlength-max'     => 5,
                        'data-bv-stringlength-message' => __("It's over limit characters (%s).", 5),
                    ))
            ?>
        </div>
    </td>
    <td>
        <div class="form-group">

            <?= $this->Form
                ->input("EvaluateScore.$index.description",
                    array(
                        'wrapInput'                    => false,
                        'type'                         => 'textarea',
                        'rows'                         => 3,
                        'div'                          => false,
                        'label'                        => false,
                        'class'                        => 'form-control',
                        'placeholder'                  => __('Please explain the score.'),
                        'data-bv-notempty-message'     => __("Input is required."),
                        'required'                     => true,
                        'data-bv-stringlength'         => 'true',
                        'data-bv-stringlength-max'     => 2000,
                        'data-bv-stringlength-message' => __("It's over limit characters (%s).", 2000),
                    ))
            ?>
        </div>
    </td>
    <?php if (viaIsSet($type) && viaIsSet($id) && $type == 'exists'): ?>
        <td>
            <a class="modal-ajax-get"
               href="#"
               data-url="<?= $this->Html->url([
                   'controller' => 'teams',
                   'action'     => 'ajax_get_confirm_inactive_score_modal',
                   'team_id'    => $id
               ]) ?>"><i
                    class="fa fa-trash font_lightGray-gray"></i></a>
        </td>
        <?= $this->Form->hidden("EvaluateScore.$index.id") ?>
    <?php else: ?>
        <td>
            <a href="#" class="click-target-remove" target-selector="#AddScore_<?= $index ?>"><i
                    class="fa fa-times font_lightGray-gray"></i></a>
        </td>
    <?php endif; ?>
</tr>
<?= $this->App->viewEndComment() ?>

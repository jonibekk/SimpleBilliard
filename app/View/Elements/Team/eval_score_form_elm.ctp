<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 *
 * @var CodeCompletionView $this
 */
?>
<!-- START app/View/Elements/Team/eval_score_form_elm.ctp -->
<tr class="evaluation_select"
    index="<?php echo $es_key ?>">
    <td>
        <?= $this->Form
            ->input("EvaluateScore.$es_key.name",
                    array(
                        'wrapInput'   => false,
                        'type'        => 'text',
                        'div'         => false,
                        'label'       => false,
                        'class'       => 'form-control',
                        'placeholder' => __d('team', '名前'),
                    ))
        ?>
    </td>
    <td>
        <?= $this->Form
            ->input("EvaluateScore.$es_key.index_num",
                    array(
                        'wrapInput'   => false,
                        'type'        => 'number',
                        'div'         => false,
                        'label'       => false,
                        'class'       => 'form-control',
                        'placeholder' => __d('team', '表示順'),
                    ))
        ?>

    </td>
    <td>
        <?= $this->Form
            ->input("EvaluateScore.$es_key.description",
                    array(
                        'wrapInput'   => false,
                        'type'        => 'textarea',
                        'rows'        => 3,
                        'div'         => false,
                        'label'       => false,
                        'class'       => 'form-control',
                        'placeholder' => __d('team', '定義の説明を書きましょう'),
                    ))
        ?>
    </td>
    <?= $this->Form->hidden("EvaluateScore.$es_key.id") ?>
    <?= $this->Form->hidden("EvaluateScore.$es_key.team_id") ?>
</tr>
<!-- END app/View/Elements/Team/eval_score_form_elm.ctp -->

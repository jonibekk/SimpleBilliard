<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 7/9/15
 * Time: 3:33 PM
 *
 * @var CodeCompletionView $this
 * @var                    $goal_list
 * @var                    $goal_id
 */
?>
<!-- START app/View/Users/view_actions.ctp -->
<div class="col-sm-8 col-sm-offset-2">
    <div class="panel panel-default">
        <?= $this->element('simplex_top_section') ?>
        <div class="panel-body">
            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-addon" id=""><i class="fa fa-flag"></i></span>
                    <?=
                    $this->Form->input('goal_id', [
                        'label'                    => false,
                        'div'                      => false,
                        'required'                 => true,
                        'data-bv-notempty-message' => __d('validate', "入力必須項目です。"),
                        'class'                    => 'form-control',
                        'id'                       => 'GoalSelectForm',
                        'options'                  => $goal_list,
                        'default'                  => $goal_id,
                    ])
                    ?>
                </div>
            </div>
        </div>
        <div class="panel-body">
            <div class="pull-right">
                <ul class="list-inline">
                    <li>
                        <a href="<?= $this->Html->url(array_merge($this->request->params['named'],
                                                                  ['page_type' => 'image'])) ?>">
                            <i class="fa fa-th-large link-dark-gray"></i>
                        </a>
                    </li>
                    <li>
                        <a href="<?= $this->Html->url(array_merge($this->request->params['named'],
                                                                  ['page_type' => 'list'])) ?>">
                            <i class="fa fa-reorder link-dark-gray"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="panel-body">

        </div>
    </div>
</div>
<!-- END app/View/Users/view_actions.ctp -->

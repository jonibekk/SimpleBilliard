<?
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/11/14
 * Time: 11:40 AM

 *
*@var View    $this
 * @var         $this CodeCompletionView
 * @var         $goal_category_list
 * @var         $priority_list
 */
?>
<!-- START app/View/Goals/add.ctp -->
<div class="row">
    <div class="col-sm-8 col-sm-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading"><?= __d('gl', "ゴールを作成してください") ?></div>
            <?=
            $this->Form->create('Goal', [
                'inputDefaults' => [
                    'div'       => 'form-group',
                    'label'     => [
                        'class' => 'col col-sm-3 control-label'
                    ],
                    'wrapInput' => 'col col-sm-6',
                    'class'     => 'form-control addteam_input-design'
                ],
                'class'         => 'form-horizontal',
                'novalidate'    => true,
                'type'          => 'file',
            ]); ?>
            <div class="panel-body add-team-panel-body">
                <?=
                $this->Form->input('purpose',
                                   ['label'       => __d('gl', "目的"),
                                    'placeholder' => __d('gl', "例) 昇級したい"),
                                    'rows' => 1,
                                   ]) ?>
                <hr>
                <?=
                $this->Form->input('goal_category_id', [
                    'label'   => __d('gl', "カテゴリ"),
                    'type'    => 'select',
                    'options' => $goal_category_list,
                ])?>
                <hr>
                <?=
                $this->Form->input('priority', [
                    'label'    => __d('gl', "重要度"),
                    'type'     => 'select',
                    'default'  => 3,
                    'required' => false,
                    'style'    => 'width:50px',
                    'options'  => $priority_list,
                ])?>
                <hr>
                <div class="form-group">
                    <label for="" class="col col-sm-3 control-label"><?= __d('gl', "ゴール画像") ?></label>

                    <div class="col col-sm-6">
                        <div class="fileinput fileinput-new" data-provides="fileinput">
                            <div class="fileinput-preview thumbnail nailthumb-container photo-design"
                                 data-trigger="fileinput"
                                 style="width: 150px; height: 150px;">
                                <i class="fa fa-plus photo-plus-large"></i>
                            </div>
                            <div>
                        <span class="btn btn-default btn-file">
                            <span class="fileinput-new">
                                <?=
                                __d('gl',
                                    "画像を選択") ?>
                            </span>
                            <span class="fileinput-exists"><?= __d('gl', "画像を再選択") ?></span>
                            <?=
                            $this->Form->input('photo',
                                               ['type'         => 'file',
                                                'label'        => false,
                                                'div'          => false,
                                                'css'          => false,
                                                'wrapInput'    => false,
                                                'errorMessage' => false,
                                                ''
                                               ]) ?>
                        </span>
                            </div>
                        </div>
                        <span class="help-block"><?= __d('gl', '10MB以下') ?></span>

                        <div class="has-error">
                            <?=
                            $this->Form->error('photo', null,
                                               ['class' => 'help-block text-danger',
                                                'wrap'  => 'span'
                                               ]) ?>
                        </div>
                    </div>

                </div>
            </div>

            <div class="panel-footer addteam_pannel-footer">
                <div class="row">
                    <div class="col-sm-9 col-sm-offset-3">
                        <?=
                        $this->Form->submit(__d('gl', "ゴールを作成"),
                                            ['class' => 'btn btn-primary', 'div' => false]) ?>
                    </div>
                </div>
            </div>
            <?= $this->Form->end(); ?>
        </div>
    </div>
</div>
<!-- END app/View/Goals/add.ctp -->

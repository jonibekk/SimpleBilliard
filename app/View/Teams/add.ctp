<?
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/11/14
 * Time: 11:40 AM
 *
 * @var View $this
 * @var      $this CodeCompletionView
 */
?>
<div class="row">
    <div class="col-sm-8 col-sm-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading"><?= __d('gl', "チームを作成してください") ?></div>
            <?=
            $this->Form->create('Team', [
                'inputDefaults' => [
                    'div'       => 'form-group',
                    'label'     => [
                        'class' => 'col col-sm-3 control-label'
                    ],
                    'wrapInput' => 'col col-sm-6',
                    'class'     => 'form-control'
                ],
                'class'         => 'form-horizontal validate',
                'novalidate'    => true,
                'type'          => 'file',
            ]); ?>
            <div class="panel-body">
                <?=
                $this->Form->input('name',
                                   ['label'                    => __d('gl', "チーム名"),
                                    'placeholder'              => __d('gl', "例) チームGoalous"),
                                    "data-bv-notempty-message" => __d('validate', "入力必須項目です。"),
                                   ]) ?>
                <hr>
                <div class="form-group">
                    <label for="" class="col col-sm-3 control-label"><?= __d('gl', "チーム画像") ?></label>

                    <div class="col col-sm-6">
                        <div class="fileinput fileinput-new" data-provides="fileinput">
                            <div class="fileinput-preview thumbnail nailthumb-container" data-trigger="fileinput"
                                 style="width: 150px; height: 150px;">
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
                <hr>
                <?=
                $this->Form->input('type', [
                    'label'      => __d('gl', "プラン"),
                    'type'       => 'select',
                    'options'    => Team::$TYPE,
                    'afterInput' => '<span class="help-block">' . __d('gl',
                                                                      "フリープランは、５人までのチームで使えます。また、複数の機能制限がございます。") . '<br>' . __d('gl',
                                                                                                                                "このプランはチーム作成後にいつでも変更できます。") . '</span>'
                ])?>
            </div>

            <div class="panel-footer">
                <div class="row">
                    <div class="col-sm-9 col-sm-offset-3">
                        <?=
                        $this->Form->submit(__d('gl', "チームを作成"),
                                            ['class' => 'btn btn-primary', 'div' => false]) ?>
                        <?=
                        $this->Html->link(__d('gl', "スキップ"), "/",
                                          ['class' => 'btn btn-default', 'div' => false]) ?>
                    </div>
                </div>
            </div>
            <?= $this->Form->end(); ?>
        </div>
    </div>
</div>

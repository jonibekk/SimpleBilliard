<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/30/14
 * Time: 9:59 AM
 *
 * @var CodeCompletionView $this
 * @var array              $my_teams
 */
?>
    <div class="row">
        <div class="col-sm-8 col-sm-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading"><?= __d('gl', "新しいメンバーを招待") ?></div>
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
                    'class'         => 'form-horizontal',
                    'novalidate'    => true,
                    'id'            => 'InviteTeamForm',
                ]); ?>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="TeamName" class="col col-sm-3 control-label"><?= __d('gl', "チーム名") ?></label>

                        <div class="col col-sm-6">
                            <p class="form-control-static"><?= h($my_teams[$this->Session->read('current_team_id')]) ?></p>
                        </div>
                    </div>
                    <hr>
                    <?=
                    $this->Form->input('emails', [
                        'label'                    => __d('gl', "招待するメンバーのメールアドレス"),
                        'type'                     => 'text',
                        'rows'                     => 3,
                        "data-bv-notempty-message" => __d('validate', "入力必須項目です。"),
                        'afterInput'               => '<span class="help-block">'
                            . '<ul class="example-indent"><li>' . __d('gl', "例%s",
                                                                      1) . ' aaa@example.com,bbb@example.com</li></ul>'
                            . '<ul class="example-indent"><li>'
                            . '' . __d('gl', "例%s", 2) . ' aaa@example.com</br>'
                            . 'aaa@example.com</br>'
                            . '</li></ul>'
                            . '</span>'
                    ])?>
                    <hr>
                    <?=
                    $this->Form->input('comment', [
                        'label'      => __d('gl', "コメント(オプション)"),
                        'type'       => 'text',
                        'rows'       => 3,
                        'afterInput' => '<span class="help-block">' . __d('gl', "コメント(任意)はメールの本文に追加されます。") . '</span>'
                    ])?>
                </div>
                <div class="panel-footer">
                    <div class="row">
                        <div class="col-sm-9 col-sm-offset-3">
                            <?=
                            $this->Form->submit(__d('gl', "招待"),
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
<? $this->append('script') ?>
    <script type="text/javascript">
        $(document).ready(function () {

            $('[rel="tooltip"]').tooltip();

            $('#InviteTeamForm').bootstrapValidator({
                live: 'enabled',
                feedbackIcons: {
                    valid: 'fa fa-check',
                    invalid: 'fa fa-times',
                    validating: 'fa fa-refresh'
                }
            });
        });
    </script>
<? $this->end() ?>
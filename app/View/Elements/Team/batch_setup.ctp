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
<!-- START app/View/Elements/Team/batch_setup.ctp -->
<div class="panel panel-default">
    <div class="panel-heading"><?= __d('gl', "一括登録") ?></div>
    <div class="panel-body">
        <div class="form-group">
            <label for="TeamName" class="col col-sm-3 control-label form-label"></label>

            <div class="col col-sm-6">
                <p class="form-control-static"><?= __d('gl', "チームメンバーの登録や更新をCSVで一括処理します。") ?></p>

                <p class="form-control-static"><?= __d('gl',
                                                       "ファイルをダウンロードし、フォーマットに従って入力したあと、更新済みのCSVファイルをアップロードしてください。") ?></p>

                <p class="form-control-static"><?= __d('gl', "すでに登録済のメンバーはデータ更新され、新規のメンバーは追加で登録され、招待メールが送信されます。") ?></p>

                <p class="form-control-static"><?= __d('gl', "") ?></p>

                <p class="form-control-static"><?= __d('gl', "") ?></p>

                <p class="form-control-static"><?= __d('gl', "") ?></p>

                <p class="form-control-static"><?= __d('gl', "") ?></p>
            </div>
        </div>
    </div>
    <div class="panel-footer">
        <div class="row">
            <div class="col-sm-9 col-sm-offset-3">
                <?=
                $this->Form->postLink(__d('gl', "新規ダウンロード"), ['action' => 'download_add_members_csv_format'],
                                      ['class' => 'btn btn-default', 'div' => false])
                ?>
                <?=
                $this->Form->postLink(__d('gl', "新規アップロード"), ['action' => 'download_add_members_csv_format'],
                                      ['class' => 'btn btn-primary', 'div' => false])
                ?>
            </div>
        </div>
    </div>
    <div class="panel-footer">
        <div class="row">
            <div class="col-sm-9 col-sm-offset-3">
                <?=
                $this->Form->postLink(__d('gl', "ダウンロード"), ['action' => 'download_team_members_csv'],
                                      ['class' => 'btn btn-default', 'div' => false]) ?>
                <?=
                $this->Form->postLink(__d('gl', "アップロード"), "/",
                                      ['class' => 'btn btn-primary', 'div' => false]) ?>

            </div>
        </div>
    </div>
</div>
<!-- END app/View/Elements/Team/batch_setup.ctp -->

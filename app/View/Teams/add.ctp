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
    <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading"><?= __d('gl', "チームを作成してください") ?></div>
            <div class="panel-body">
            </div>
            <div class="panel-footer">
                <div class="row">
                    <div class="col-md-9 col-md-offset-3">
                        <?=
                        $this->Form->submit(__d('gl', "チームを作成"),
                                            ['class' => 'btn btn-primary', 'div' => false]) ?>
                        <?=
                        $this->Html->link(__d('gl', "スキップ"), "/",
                                          ['class' => 'btn btn-default', 'div' => false]) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

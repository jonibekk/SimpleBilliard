<?
/**
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Pages
 * @since         CakePHP(tm) v 0.10.0.1076
 * @var CodeCompletionView $this
 */
?>
<!-- START app/View/Evaluations/index.ctp -->

<div class="panel panel-default col-sm-8 col-sm-offset-2 clearfix">
    <div class="panel-heading"><?= __d('gl', "評価") ?></div>
    <?=
    $this->Form->create('EvalIndex', [
        'inputDefaults' => [
            'div'       => 'form-group',
            'label'     => [
                'class' => 'col col-xxs-12 col-sm-3 control-label form-label'
            ],
            'wrapInput' => 'col col-sm-6',
            'class'     => 'form-control'
        ],
        'class'         => 'form-horizontal',
        //        'novalidate'    => true,
        //        'id'            => 'UserAccountForm',
    ]); ?>
    <div class="panel-body eval-view-panel-body">
        <div class="col-sm-12 bg-danger font_bold p_8px mb_8px"><?= __d('gl',"あと1件の評価が完了しておりません。以下より評価を行なってください。") ?></div>
        <div class="form-group">
            <hr>
            <div for="#" class="col col-sm-12 eval-index-panel-title bg-lightGray p_8px mb_8px">
                <p class="font_bold"><?= __d('gl', "自分") ?></p>
                <p><?= __d('gl', "未完了:") ?></p> <!-- ToDo 0の場合は表示しない-->
            </div>
            <div class="col-xxs-12 mb_8px">
                <div class="col-xxs-1">
                    <img src="../../img/logo_on.png" width="48" height="48" alt="You" class="eval-view-panel-goal-pic">
                </div>
                <div class="col-xxs-11">
                    <p><?= __d('gl',"菊池厚平") ?></p>
                    <span><?= __d('gl',"あなた") ?></span><i class="fa fa-long-arrow-right"></i><span><?= __d('gl',"最終者") ?></span>
                    <p class="font_brownRed"><?= __d('gl',"自己評価をしてください") ?></p>
                </div>
            </div>
            <hr class="col-xxs-12">
            <div for="#" class="col col-sm-12 eval-index-panel-title bg-lightGray p_8px mb_8px">
                <p class="font_bold"><?= __d('gl', "あなたがコーチのメンバー") ?></p>
                <p><?= __d('gl', "未完了:") ?></p> <!-- ToDo 0の場合は表示しない-->
            </div>
            <div class="col-xxs-12 mb_8px">
                <div class="col-xxs-1">
                    <img src="../../img/logo_on.png" width="48" height="48" alt="You" class="eval-view-panel-goal-pic">
                </div>
                <div class="col-xxs-11">
                    <p class="font_bold"><?= __d('gl',"平形大樹") ?></p>
                    <span><?= __d('gl',"メンバー") ?></span><i class="fa fa-long-arrow-right"></i><span><?= __d('gl',"あなた") ?></span><i class="fa fa-long-arrow-right"></i><span><?= __d('gl',"最終者") ?></span>
                    <p class="font_verydark"><?= __d('gl',"メンバーの評価待ち") ?></p>
                </div>
            </div>
            <hr class="col-xxs-12">
            <div class="col-xxs-12 mb_8px">
                <div class="col-xxs-1">
                    <img src="../../img/logo_on.png" width="48" height="48" alt="You" class="eval-view-panel-goal-pic">
                </div>
                <div class="col-xxs-11">
                    <p class="font_bold"><?= __d('gl',"小嶋太郎") ?></p>
                    <span><?= __d('gl',"メンバー") ?></span><i class="fa fa-long-arrow-right"></i><span><?= __d('gl',"あなた") ?></span><i class="fa fa-long-arrow-right"></i><span><?= __d('gl',"最終者") ?></span>
                    <p class="font_verydark"><?= __d('gl',"メンバーの評価待ち") ?></p>
                </div>
            </div>
        </div>
        <hr>


        <?= $this->Form->submit(__d('gl', "変更を保存"), ['class' => 'btn btn-primary pull-right']) ?>

        <div class="clearfix"></div>
    </div>
    <?= $this->Form->end(); ?>
</div>
<? $this->append('script') ?>
<? $this->end() ?>
<!-- END app/View/Evaluations/index.ctp -->

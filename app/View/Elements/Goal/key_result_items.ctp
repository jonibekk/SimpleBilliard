<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 9/26/14
 * Time: 11:14 AM
 *
 * @var CodeCompletionView $this
 * @var                    $key_results
 * @var                    $kr_can_edit
 * @var                    $incomplete_kr_count
 * @var                    $goal_id
 */
?>
<!-- START app/View/Elements/Goal/key_result_items.ctp -->

<!-- ToDo -> 大樹さん、完了したKRかどうかでアイコンのクラスを変更する処理もお願いします。 -->
<ul class="dashboard-goals-card-body-krs-wrap">
    <li class="dashboard-goals-card-body-krs">
        <i class="fa-key fa dashboard-goals-card-body-krs-icon-unachieved"></i>
        <p class="dashboard-goals-card-body-krs-title-unachieved">達成などしていないKRです。</p>
        <a class="fa-check fa dashboard-goals-card-body-krs-action"></a>
    </li>
    <li class="dashboard-goals-card-body-krs">
        <i class="fa-key fa dashboard-goals-card-body-krs-icon-achieved"></i>
        <p class="dashboard-goals-card-body-krs-title-achieved">達成済のKRです。</p>
    </li>
    <li class="dashboard-goals-card-body-krs">
        <a href="" class="hoge">
            <i class="fa-key fa dashboard-goals-card-body-krs-icon-achieved"></i>
            <p class="dashboard-goals-card-body-krs-title-unachieved">文字数をめちゃくちゃ長くしたバージョンを入れておかないと後々に困ります。</p>
        </a>
        <a class="fa-check fa dashboard-goals-card-body-krs-action"></a>
    </li>
    <li>
        <a class="dashboard-goals-card-body-add-kr-link" href="">
            <i class="fa fa-plus-circle font_brownRed"></i>
            <span class="ml_2px"><?= __d('gl', "達成要素を追加") ?></span>
        </a>
    </li>

</ul>


<!-- 1>0のときとかいう条件付けて読み込まないようにしてありますｗ -->
<?php if (0>1): ?>
    <?php if (!empty($key_results)): ?>
        <?php foreach ($key_results as $kr): ?>
            <div class="bd-t h_50px">
                <div class="col col-xxs-12 responsive-goal-space-width mxh_50px ln_1 ptb_5px">
                    <div class="inline-block responsive-goal-title-width pl_1px">
                        <span class="ln_1 tap-btn-text font_verydark kr-text">
                            <?= h($kr['KeyResult']['name']) ?></span>
                        <i class="fa fa-check-circle"></i>
                        <span class="ml_2px"><?= h($kr['KeyResult']['action_result_count']) ?></span>
                        <?php if ($kr['KeyResult']['completed']): ?>
                            <span class="fin-kr tag-sm tag-info"><?= __d('gl', "完了") ?></span>
                        <?php else: ?>
                            <span class="unfin-kr tag-sm tag-danger"><?= __d('gl', "未完了") ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if ($kr_can_edit): ?>
                    <?= $this->element('Goal/key_result_edit_button', ['kr' => $kr]) ?>
                <?php endif; ?>
            </div>
        <?php endforeach ?>
        <?php if ($kr_can_edit): ?>
            <div class="bd-t pt_8px">
                <a class="col col-xxs-12 bd-dash font_lightGray-gray p_10px modal-ajax-get-add-key-result"
                   href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_add_key_result_modal', 'goal_id' => $goal_id]) ?>">
                    <i class="fa fa-plus-circle font_brownRed"></i>
                    <span class="ml_2px"><?= __d('gl', "達成要素を追加") ?></span>
                </a>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="col col-xxs-12">
            <div class="bd-t pt_8px">
                <a class="col col-xxs-12 bd-dash font_lightGray-gray p_10px modal-ajax-get-add-key-result"
                   href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_add_key_result_modal', 'goal_id' => $goal_id]) ?>">
                    <i class="fa fa-plus-circle font_brownRed"></i>
                    <span class="ml_2px"><?= __d('gl', "達成要素を追加") ?></span>
                </a>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>
<!-- End app/View/Elements/Goal/key_result_items.ctp -->

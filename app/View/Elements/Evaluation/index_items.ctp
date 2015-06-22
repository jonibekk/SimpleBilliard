<?php /**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 3/26/15
 * Time: 3:45 PM
 *
 * @var CodeCompletionView $this
 * @var                    $evaluatees
 * @var                    $eval_term_id
 * @var                    $eval_is_frozen
 */
?>
<!-- START app/View/Elements/Evaluation/index_items.ctp -->
<?php foreach ($evaluatees as $user): ?>
    <a href="<?= $this->Html->url(['controller' => 'evaluations', 'action' => 'view', 'evaluate_term_id' => $eval_term_id, 'user_id' => $user['User']['id']]) ?>"
       class="font_verydark">
        <div class="col-xxs-12 mb_8px">
            <div class="disp_ib">
                <?=
                $this->Upload->uploadImage($user, 'User.photo', ['style' => 'medium'],
                                           ['width' => '48px', 'height' => '48px', 'alt' => 'icon', 'class' => 'pull-left img-circle mtb_3px']) ?>
            </div>
            <div class="disp_ib ml_8px">
                <p class="font_bold"><?= h($user['User']['display_username']) ?></p>
                <?php foreach ($user['flow'] as $k => $v): ?>
                    <?php if ($k !== 0): ?>&nbsp;<i class="fa fa-long-arrow-right"></i>&nbsp;<?php endif ?>
                    <span>
                        <?= $v['this_turn'] ? '<b>' . $v['name'] . '</b>' : $v['name'] ?>
                    </span>
                <?php endforeach ?>
                <?php if ($user['status_text']['body'] && !$eval_is_frozen): ?>
                    <?php $text_class = $user['status_text']['your_turn'] ? 'font_brownRed' : 'font_verydark' ?>
                    <p class="<?= $text_class ?>"><?= $user['status_text']['body'] ?></p>
                <?php else: ?>
                    <p class="font_verydark"></p>
                <?php endif; ?>
            </div>
        </div>
    </a>
    <hr class="col-xxs-12">
<?php endforeach; ?>
<!-- END app/View/Elements/Evaluation/index_items.ctp -->

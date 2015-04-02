<?
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 3/26/15
 * Time: 3:45 PM
 *
 * @var $evaluatees
 * @var $eval_term_id
 */
?>
<!-- START app/View/Elements/Evaluation/index_items.ctp -->
<? foreach ($evaluatees as $user): ?>
    <a href="<?= $this->Html->url(['controller' => 'evaluations', 'action' => 'view', $eval_term_id, $user['User']['id']]) ?>"
       class="font_verydark">
        <div class="col-xxs-12 mb_8px">
            <div class="disp_ib">
                <?=
                $this->Upload->uploadImage($user, 'User.photo', ['style' => 'medium'],
                                           ['width' => '48px', 'height' => '48px', 'alt' => 'icon', 'class' => 'pull-left img-circle mtb_3px']) ?>
            </div>
            <div class="disp_ib ml_8px">
                <p class="font_bold"><?= h($user['User']['display_username']) ?></p>
                <? foreach ($user['flow'] as $k => $v): ?>
                    <? if ($k !== 0): ?>&nbsp;<i class="fa fa-long-arrow-right"></i>&nbsp;<? endif ?>
                    <span>
                        <?= $v['this_turn'] ? '<b>' . $v['name'] . '</b>' : $v['name'] ?>
                    </span>
                <? endforeach ?>
                <? if ($user['status_text']['body']): ?>
                    <? $text_class = $user['status_text']['your_turn'] ? 'font_brownRed' : 'font_verydark' ?>
                    <p class="<?= $text_class ?>"><?= $user['status_text']['body'] ?></p>
                <? endif; ?>
            </div>
        </div>
    </a>
    <hr class="col-xxs-12">
<? endforeach; ?>
<!-- END app/View/Elements/Evaluation/index_items.ctp -->

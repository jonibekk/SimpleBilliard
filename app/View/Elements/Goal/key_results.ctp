<?= $this->App->viewStartComment() ?>
<?php if ($key_results): ?>
    <?php foreach ($key_results as $kr): ?>
        <div class="goal-detail-kr-card">
            <div class="goal-detail-kr-flex">
                <div class="goal-detail-kr-block-main">
                    <h4 class="goal-detail-kr-card-title">
                        <?= h($kr['KeyResult']['name']) ?>
                    </h4>
                    <?php if ($kr['KeyResult']['completed']): ?>
                        <?= __('Clear') ?>
                    <?php endif ?>

                    <!--progress bar-->
                    <?php // TODO:KR進捗機能がリリースされ次第、進捗率に合わせたバーの塗りつぶしと「{現在の進捗}/{目標値}」の表示に変える ?>
                    <div class="goal-detail-kr-progress-bar-wrap mtb_4px">
                        <span class="goal-detail-kr-progress-text"><?= $kr['KeyResult']['display_value'] ?></span>
                        <div class="goal-detail-kr-progress-bar mod-incomplete rate-0"></div>
                    </div>
                    <dl class="goal-detail-kr-info-counts mb_4px">
                        <dt class="goal-detail-kr-info-counts-title"><i class="fa fa-check-circle"></i></dt>
                        <dd class="goal-detail-kr-info-counts-description">
                            <?= count($kr['ActionResult'])?>
                        </dd>
                        <dt class="goal-detail-kr-info-counts-title"><i class="fa fa-database"></i></dt>
                        <dd class="goal-detail-kr-info-counts-description">
                            <?php if ($kr['KeyResult']['tkr_flg']):?>
                                TKR
                            <?php else:?>
                                <?=$kr['KeyResult']['priority']?>
                            <?php endif;?>
                        </dd>
                        <dt class="goal-detail-kr-info-counts-title"><i class="fa fa-clock-o"></i></dt>
                        <dd class="goal-detail-kr-info-counts-description">
                            <?= $this->Time->format('Y/m/d', $kr['KeyResult']['end_date'] + $goal_term['timezone'] * HOUR) ?>
                            <?php if ($this->Session->read('Auth.User.timezone') != $goal_term['timezone']): ?>
                                <?= $this->TimeEx->getTimezoneText($goal_term['timezone']); ?>
                            <?php endif ?>
                        </dd>
                    </dl>
                </div>
                <div class="goal-detail-kr-block-side">
                    <?php if ($kr_can_edit): ?>
                        <?= $this->element('Goal/key_result_edit_menu_dropdown', ['kr' => $kr]) ?>
                    <?php endif ?>
                </div>
            </div>
            <?php if (!empty($kr['KeyResult']['description'])):?>
                <div class="showmore-init-none">
                    <?= nl2br($kr['KeyResult']['description']) ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach ?>
<?php endif ?>
<?= $this->App->viewEndComment() ?>

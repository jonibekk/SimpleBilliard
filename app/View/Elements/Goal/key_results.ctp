<?= $this->App->viewStartComment() ?>
<?php if ($key_results): ?>
    <?php foreach ($key_results as $kr): ?>
        <div class="goal-detail-kr-card">
            <div class="goal-detail-kr-flex mb_4px">
                <div class="goal-detail-kr-block-main">
                    <h4 class="goal-detail-kr-card-title">
                        <?= h($kr['KeyResult']['name']) ?>
                    </h4>
                </div>
                <div class="goal-detail-kr-block-side">
                    <?php if ($kr_can_edit): ?>
                        <?= $this->element('Goal/key_result_edit_menu_dropdown', ['kr' => $kr]) ?>
                    <?php endif ?>
                </div>
            </div>
            <!--progress bar-->
            <?= $this->element('KeyResult/progress_bar', ['kr' => $kr['KeyResult']]) ?>

            <dl class="goal-detail-kr-info-counts mb_4px">
                <dt class="goal-detail-kr-info-counts-title"><i class="fa fa-check-circle"></i></dt>
                <dd class="goal-detail-kr-info-counts-description">
                    <?= $kr['KeyResult']['action_result_count'] ?>
                </dd>
                <dt class="goal-detail-kr-info-counts-title"><i class="fa fa-database"></i></dt>
                <dd class="goal-detail-kr-info-counts-description">
                    <?php if ($kr['KeyResult']['tkr_flg']): ?>
                        TKR
                    <?php else: ?>
                        <?= $kr['KeyResult']['priority'] ?>
                    <?php endif; ?>
                </dd>
                <dt class="goal-detail-kr-info-counts-title"><i class="fa fa-clock-o"></i></dt>
                <dd class="goal-detail-kr-info-counts-description">
                    <?= AppUtil::dateYmdReformat($kr['KeyResult']['end_date'], "/") ?>
                    <?php if ($this->Session->read('Auth.User.timezone') != $goal_term['timezone']): ?>
                        <?= $this->TimeEx->getTimezoneText($goal_term['timezone']); ?>
                    <?php endif ?>
                </dd>
                <dt class="goal-detail-kr-info-counts-title <?= (true ? 'active' : '') ?>">
                    <i class="fa fa-eye"></i>
                </dt>
                <dd class="goal-detail-kr-info-counts-description <?= (true ? 'active' : '') ?>">
                    <?php if (true) : ?>
                        <?= __("Watched") ?>
                    <?php else : ?>
                        <?= __("Watch") ?>
                    <?php endif; ?>
                </dd>
            </dl>
            <?php if (!empty($kr['KeyResult']['description'])): ?>
                <div class="showmore-init-none">
                    <?= nl2br($kr['KeyResult']['description']) ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach ?>
<?php endif ?>
<?= $this->App->viewEndComment() ?>

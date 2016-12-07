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
            <?php
                $currentValue = $this->NumberEx->formatProgressValue($kr['KeyResult']['current_value'], $kr['KeyResult']['value_unit']);
                $shortCurrentValue = $this->NumberEx->shortFormatProgressValue($kr['KeyResult']['current_value'], $kr['KeyResult']['value_unit']);

                $startValue = "";
                $targetValue = "";
                $shortStartValue = "";
                $shortTargetValue = "";
                if ($kr['KeyResult']['value_unit'] != KeyResult::UNIT_BINARY) {
                    $startValue = $this->NumberEx->formatProgressValue($kr['KeyResult']['start_value'], $kr['KeyResult']['value_unit']);
                    $targetValue = $this->NumberEx->formatProgressValue($kr['KeyResult']['target_value'], $kr['KeyResult']['value_unit']);
                    $shortStartValue = $this->NumberEx->shortFormatProgressValue($kr['KeyResult']['start_value'], $kr['KeyResult']['value_unit']);
                    $shortTargetValue = $this->NumberEx->shortFormatProgressValue($kr['KeyResult']['target_value'], $kr['KeyResult']['value_unit']);
                }
            ?>
            <div class="goal-detail-kr-progress-block js-show-detail-progress-value"
                 data-current_value="<?=$currentValue?>"
                 data-start_value="<?=$startValue?>"
                 data-target_value="<?=$targetValue?>"
            >
                <div class="goal-detail-kr-progress-bar-wrap">
                    <span class="goal-detail-kr-progress-text">
                        <?= $shortCurrentValue ?>
                    </span>
                    <?php $progressClass = $kr['KeyResult']['progress_rate'] == 100 ? "mod-complete" : "mod-incomplete rate-".$kr['KeyResult']['progress_rate']?>
                    <div class="goal-detail-kr-progress-bar <?=$progressClass?>"></div>
                </div>
                <div class="goal-detail-kr-progress-values">
                    <div class="goal-detail-kr-progress-values-left">
                        <span>
                            <?= $shortStartValue ?>
                        </span>
                    </div>
                    <div class="goal-detail-kr-progress-values-right">
                        <span>
                            <?= $shortTargetValue ?>
                        </span>
                    </div>
                </div>
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
            <?php if (!empty($kr['KeyResult']['description'])):?>
                <div class="showmore-init-none">
                    <?= nl2br($kr['KeyResult']['description']) ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach ?>
<?php endif ?>
<?= $this->App->viewEndComment() ?>

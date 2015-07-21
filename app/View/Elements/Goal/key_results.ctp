<?php
/**
 * @var $key_results
 * @var $incomplete_kr_count
 * @var $kr_can_edit
 */
?>
<?php if ($key_results): ?>
    <!-- START app/View/Elements/Goal/key_results.ctp -->
    <?php foreach ($key_results as $kr): ?>
        <div style="padding:3px; margin:3px;">

            <?php if (isset($kr_can_edit) && $kr_can_edit): ?>
                <?= $this->element('Goal/key_result_edit_button', ['kr' => $kr]) ?>
            <?php endif ?>

            <div>
                <strong><?= h($kr['KeyResult']['name']) ?></strong>
                <?php if ($kr['KeyResult']['completed']): ?>
                    <?= __d('gl', 'クリア') ?>
                <?php endif ?>
                <br>
                <i class="fa fa-check-circle"></i>
                <?= h($kr['KeyResult']['action_result_count']) ?>
                <br>
                <?= h(round($kr['KeyResult']['start_value'],
                            1)) ?><?= h(KeyResult::$UNIT[$kr['KeyResult']['value_unit']]) ?> →
                <?= h(round($kr['KeyResult']['target_value'],
                            1)) ?><?= h(KeyResult::$UNIT[$kr['KeyResult']['value_unit']]) ?>
                <br>
                <?= $this->Time->format('Y/m/d',
                                        $kr['KeyResult']['start_date'] + $this->Session->read('Auth.User.timezone') * 3600) ?>
                →
                <?= $this->Time->format('Y/m/d',
                                        $kr['KeyResult']['end_date'] + $this->Session->read('Auth.User.timezone') * 3600) ?>
                <br>
            </div>

        </div>
    <?php endforeach ?>
    <!-- END app/View/Elements/Goal/key_results.ctp -->
<?php endif ?>
<?php
$units = Hash::combine(Configure::read("label.units"), '{n}.id', '{n}.unit');
?>
<?php foreach ($krs as $i => $kr): ?>
    <li class="action-kr-progress-edit-item js-select-kr" data-kr-id="<?= $kr['id'] ?>">
        <div class="action-kr-progress-edit-item-box">
            <div class="action-kr-progress-edit-item-box-left">
                <span class="action-kr-progress-edit-item-check-circle"></span>
            </div>
            <div class="action-kr-progress-edit-item-box-right">
                <p class="action-kr-progress-edit-item-title">
                    <?= $kr['name'] ?>
                </p>
                <div class="js-show-input-kr-progress" style="display: none;">
                    <input type="hidden" name="data[ActionResult][key_result_id]" value="<?= $kr['id'] ?>" disabled>
                    <input type="hidden" name="kr_before_value" value="<?= $kr['hash_current_value'] ?>" disabled>
                    <?php $inputName = "data[ActionResult][key_result_current_value]" ?>
                    <?php if ($kr['value_unit'] == KeyResult::UNIT_BINARY): ?>
                        <input type="checkbox" name="<?= $inputName ?>"
                               class="js-kr-progress-check-complete disable-change-warning"
                               data-size="small" data-off-text="incomplete"
                               data-on-text="complete"
                               value="1"
                        />

                    <?php else: ?>
                    <?= Hash::get($units, $kr['value_unit']) ?>
                    <?php
                        // To distinguish input min/max attribute
                        // Related: https://jira.goalous.com/browse/GL-6453
                        $isIncreaseProgress = bccomp($kr['target_value'], $kr['current_value'], 3) == 1;
                    ?>
                    <input type="number"
                           name="<?= $inputName ?>"
                           class="action-kr-progress-edit-textbox form-control mlr_4px disable-change-warning"
                           min="<?= $isIncreaseProgress ? $kr['current_value'] : $kr['target_value'] ?>"
                           max="<?= $isIncreaseProgress ? $kr['target_value'] : $kr['current_value'] ?>"
                           value="<?= $kr['current_value'] ?>"
                           step="0.001"
                           disabled
                    >／<?= $kr['target_value'] ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </li>
<?php endforeach; ?>

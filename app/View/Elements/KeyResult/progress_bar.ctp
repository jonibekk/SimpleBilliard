<?php if (!empty($kr)): ?>
    <?php
    $currentValue = $this->NumberEx->formatProgressValue($kr['current_value'],
        $kr['value_unit']);
    $shortCurrentValue = $this->NumberEx->shortFormatProgressValue($kr['current_value'],
        $kr['value_unit']);

    $startValue = "";
    $targetValue = "";
    $shortStartValue = "";
    $shortTargetValue = "";
    if ($kr['value_unit'] == KeyResult::UNIT_BINARY) {
        $progressRate = empty($kr['completed']) ? 0 : 100;
    } else if($kr['value_unit'] == KeyResult::UNIT_PERCENT) {
        $progressRate = $this->NumberEx->calcProgressRate($kr['start_value'], $kr['target_value'], $kr['current_value']);
        $startValue = $this->NumberEx->formatProgressValue($kr['start_value'],
            $kr['value_unit']);
        $targetValue = $this->NumberEx->formatProgressValue($kr['target_value'], $kr['value_unit']);

        $shortCurrentValue = $currentValue;
        $shortStartValue = $startValue;
        $shortTargetValue = $targetValue;
    } else {
        $progressRate = $this->NumberEx->calcProgressRate($kr['start_value'], $kr['target_value'],
            $kr['current_value']);
        $startValue = $this->NumberEx->formatProgressValue($kr['start_value'], $kr['value_unit']);
        $targetValue = $this->NumberEx->formatProgressValue($kr['target_value'], $kr['value_unit']);

        $shortStartValue = $this->NumberEx->shortFormatProgressValue($kr['start_value'], $kr['value_unit']);
        $shortTargetValue = $this->NumberEx->shortFormatProgressValue($kr['target_value'], $kr['value_unit']);
    }

    $progressClass = $progressRate == 100 ? "is-complete" : "is-incomplete mod-rate" . $progressRate;
    ?>
    <div class="krProgress js-show-detail-progress-value"
         data-current_value="<?= $currentValue ?>"
         data-start_value="<?= $startValue ?>"
         data-target_value="<?= $targetValue ?>"
    >
        <div class="krProgress-bar">
        <span class="krProgress-text">
             <?= $shortCurrentValue ?>
        </span>
            <div class="krProgress-barCurrent <?= $progressClass ?>"></div>
        </div>
        <div class="krProgress-values">
            <div class="krProgress-valuesLeft">
            <span>
                 <?= $shortStartValue ?>
            </span>
            </div>
            <div class="krProgress-valuesRight">
            <span>
                 <?= $shortTargetValue ?>
            </span>
            </div>
        </div>
    </div>
<?php endif; ?>

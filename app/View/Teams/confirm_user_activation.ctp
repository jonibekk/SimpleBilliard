<?= $this->App->viewStartComment() ?>
<?php
// TODO.Payment:
// 1. Fixing design collaposed
// 2. Translation
?>
<section id="UserInvite" class="panel panel-default mod-form col-sm-8 col-sm-offset-2 clearfix gl-form">
    <div class="inviteCfmBlock">
        <h2 class="title"><?= __('Activation user'); ?></h2>
        <div class="ml_5px">
            <p><?= $displayUserName ?></p>
            </ul>
        </div>
        <!-- {is_paid_plan && -->
        <div class="inviteCfmBlock mod-bdt">
            <h2 class="title"><?= __("Billing") ?></h2>
            <div class="ml_5px">
                <dl class="totalCharge">
                    <dt class="totalCharge-label"><?= __("Total charge amount") ?>(<?= __("Tax included") ?>)</dt>
                    <dd class="totalCharge-value"><?= $totalCharge ?></dd>
                </dl>
                <div class="totalChargeFormula mb_12px">
                    <div class="totalChargeFormula-block">
                        <span class="totalChargeFormula-num"><?= $amountPerUser ?></span>
                    </div>
                    <div class="totalChargeFormula-block">
                        <span class="totalChargeFormula-symbol">×</span>
                    </div>
                    <div class="totalChargeFormula-block">
                        <div class="totalChargeFormula-fraction">
                            <span class="totalChargeFormula-num"><?= $useDaysByNext ?>
                                <span class="totalChargeFormula-unit"><?= __("days") ?></span>
                            </span>
                            <span class="totalChargeFormula-fraction-exp"></span>
                            <span class="totalChargeFormula-num">
                                1
                                <span class="totalChargeFormula-unit"><?= __("month") ?></span>
                            </span>
                        </div>
                    </div>
                </div>
                <table class="totalChargeFormulaDetail mb_16px">
                    <tbody>
                    <tr>
                        <th class="totalChargeFormulaDetail-item"><?= $amountPerUser ?></th>
                        <td class="totalChargeFormulaDetail-description">：<?= __("Price per user"); ?></td>
                    </tr>
                    <tr>
                        <th class="totalChargeFormulaDetail-item"><?= $useDaysByNext ?> days</th>
                        <td class="totalChargeFormulaDetail-description">：<?= __('Number of days'); ?>
                            (<?= __("Daily payment") ?>)
                        </td>
                    </tr>
                    </tbody>
                </table>
                <a href="/pricing" target="_blank">
                    <i class="fa fa-question-circle mr_4px" aria-hidden="true"></i><?= __("View details") ?>
                </a>
            </div>
        </div>
        <div class="serviceTermAgreement mb_8px">
            <label>
                <input type="checkbox" name="service_term_agreement"
                       class="serviceTermAgreement-cb js-required-agreement"/><?= __("I confirmed the billing content"); ?>
            </label>
        </div>
        <div class="btnGroupForForm">
            <?= $this->Form->create('TeamMember', [
                'novalidate' => true,
                'url'        => ['controller' => 'teams', 'action' => 'activate_with_payment'],
            ]); ?>
            <?= $this->Form->hidden('id', ['value' => $teamMemberId]) ?>
            <?= $this->Form->submit(__("Send"),
                ['class' => 'btnGroupForForm-next js-activate-submit', 'div' => false, 'disabled' => 'disabled']) ?>
            <?= $this->Form->end(); ?>
            <a class="btnGroupForForm-cancel" href="/teams/main"><?= __('Back'); ?></a>
        </div>
</section>

<?= $this->App->viewEndComment() ?>

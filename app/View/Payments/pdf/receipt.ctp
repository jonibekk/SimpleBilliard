<?php
/**
 * @var array $history
 * @var bool isMonthly
 * @var int maxMembers
 */
?>

<article id="pdfReceipt">
    <header>
        <figure class="logo">
            <?= $this->Html->image('homepage/Goalous_logo.png',
                [
                    'fullBase' => true
                ]
            );
            ?>
        </figure>
        <?php if ($history['PaymentSetting']['is_card']): ?>
            <h2 class="receipt-status"><?= __("PAID") ?></h2>
        <?php endif; ?>
    </header>
    <div class="invoice-overview">
        <div class="overview-item">
            <h3><?= __("Billing ID") ?></h3>
            <strong><?= h($history['ChargeHistory']['id']) ?></strong>
        </div>
        <div class="overview-item">
            <h3><?= __("Billing Date") ?></h3>
            <strong><?= h($history['ChargeHistory']['local_charge_date']) ?></strong>
        </div>
        <div class="overview-item">
            <h3><?= __('Team Name'); ?></h3>
            <strong><?= h($history['Team']['name']) ?></strong>
        </div>
        <div class="overview-item">
            <h3><?= __('Billed To'); ?></h3>
            <?php if ($history['PaymentSetting']['is_card']): ?>
                <p class="credit-charge-to">
                    <?= h($history['ChargeHistory']['total_with_currency']) ?> <?= __("charged to card ending in") ?> <?= h($history['CreditCard']['last4']) ?>
                </p>
            <?php endif; ?>
            <p><?= h($history['PaymentSetting']['contact_person_email']) ?>
                <br/> <?= h($history['PaymentSetting']['company_name']) ?></p>
        </div>
    </div>
    <div class="invoice-table">
        <?php
            $type = '';
            switch ($history['ChargeHistory']['charge_type']) {
                case Goalous\Model\Enum\ChargeHistory\ChargeType::MONTHLY_FEE:
                    $type = __("Monthly");
                    break;
                case  Goalous\Model\Enum\ChargeHistory\ChargeType::USER_INCREMENT_FEE:
                case Goalous\Model\Enum\ChargeHistory\ChargeType::USER_ACTIVATION_FEE:
                    $type = __('Add member(s)');
                    break;
                case Goalous\Model\Enum\ChargeHistory\ChargeType::UPGRADE_PLAN_DIFF:
                    $type = __('Upgrade');
                    break;
            }
            $maxMembers = $maxMembers != 0 ? $maxMembers : h($history['ChargeHistory']['charge_users']);
        ?>
        <table>
            <?php
                $label = "";
                $val = "";
                if ($isMonthly) {
                    $label = __('TIME PERIOD');
                    $val = h($history['ChargeHistory']['term']);
                } elseif ($history['ChargeHistory']['charge_type'] == Goalous\Model\Enum\ChargeHistory\ChargeType::RECHARGE) {
                    $label = __('ID BEING RECHARGED');
                    $val = implode(', ', $history['ChargeHistory']['recharge_history_ids']);
                } else {
                    $label = __('DATE');
                    $val = h($history['ChargeHistory']['local_charge_date']);
                }
            ?>

            <tbody>
            <th colspan="2"><?= __("TYPE") ?></th>
            <th><?= $label ?></th>
            <th><?= __('AMOUNT'); ?></th>
            <tr>
                <td><?= $type ?></td>
                <td><?= sprintf(__("%s members"), $maxMembers); ?></td>
                <td><?= $val ?></td>
                <td><?= h($history['ChargeHistory']['sub_total_with_currency']) ?></td>
            </tr>
            <tr>
                <td colspan="3"><?= __("Tax") ?></td>
                <td><?= h($history['ChargeHistory']['tax_with_currency']) ?></td>
            </tr>
            </tbody>
        </table>
        <footer>
            <strong class="total-label"><?= __("Total") ?></strong>
            <strong class="total-amount"><?= h($history['ChargeHistory']['total_with_currency']) ?></strong>
        </footer>
    </div>
    <div class="invoice-contact">
        <address>
            <?= __('ISAO Corporation'); ?> <br/><br/>
            <?= __('5-20-8 Asakusabashi CS Tower, 7th Floor'); ?><br/>
            <?= __('Taito-ku, Tokyo 111-0053 Japan'); ?>
        </address>
        <footer>
            <?= __('If you have any questions, please contact us at contact@goalous.com. Thank you for your business!'); ?>
        </footer>
    </div>
</article>

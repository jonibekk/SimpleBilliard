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
        <?php if (!$history['PaymentSetting']['is_card']): ?>
        <h2 class="receipt-status"><?= __("PAID") ?></h2>
        <?php endif; ?>
    </header>
    <div class="invoice-overview">
        <div class="overview-item">
            <h3><?= __("Billing ID") ?></h3>
            <strong><?= $history['ChargeHistory']['id'] ?></strong>
        </div>
        <div class="overview-item">
            <h3><?= __("Billing Date") ?></h3>
            <strong><?= $history['ChargeHistory']['local_charge_date'] ?></strong>
        </div>
        <div class="overview-item">
            <h3><?= __('Team Name');?></h3>
            <strong><?= $history['Team']['name'] ?></strong>
        </div>
        <div class="overview-item">
            <h3><?= __('Billed To');?></h3>
            <?php if ($history['PaymentSetting']['is_card']): ?>
                <p class="credit-charge-to">
                        <?= $history['ChargeHistory']['total_with_currency'] ?> <?= __("charged to card ending in")?> <?= $history['CreditCard']['last4'] ?>
                </p>
            <?php endif; ?>
            <p><?= $history['PaymentSetting']['contact_person_email'] ?> <br /> <?= $history['PaymentSetting']['company_name'] ?></p>
        </div>
    </div>
    <div class="invoice-table">
        <table>
            <tbody>
                <th colspan="2"><?= __("TYPE") ?></th>
                <th><?= $isMonthly ? __('TIME PERIOD') : __('DATE'); ?></th>
                <th><?= __('AMOUNT');?></th>
                <tr>
                    <td><?= $isMonthly ? __('Monthly') : __('Add member(s)'); ?></td>
                    <td><?= $history['ChargeHistory']['charge_users'] ?> <?= __("members") ?></td>
                    <td>
                    <?php if ($isMonthly): ?>
                        <?= $history['ChargeHistory']['term'] ?>
                    <?php else: ?>
                        <?= $history['ChargeHistory']['local_charge_date'] ?>
                    <?php endif; ?>
                    </td>
                    <td><?= $history['ChargeHistory']['sub_total_with_currency'] ?></td>
                </tr>
                <tr>
                    <td colspan="3"><?= __("Tax") ?></td>
                    <td><?= $history['ChargeHistory']['tax_with_currency'] ?></td>
                </tr>
            </tbody>
        </table>
        <footer>
            <strong class="total-label"><?= __("Total") ?></strong>
            <strong class="total-amount"><?= $history['ChargeHistory']['total_with_currency'] ?></strong>
        </footer>
    </div>
    <div class="invoice-contact">
        <address>
            <?=__('ISAO Corporation');?> <br /><br />
            <?=__('5-20-8 Asakusabashi CS Tower, 7th Floor');?><br />
            <?=__('Taito-ku, Tokyo 111-0053 Japan');?>
        </address>
        <footer>
            <?=__('If you have any questions, please contact us at contact@goalous.com. Thank you for your business!');?>
        </footer>
    </div>
</article>
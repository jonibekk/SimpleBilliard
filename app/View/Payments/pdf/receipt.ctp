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
        <h2 class="receipt-status"><?= __("PAID") ?></h2>
    </header>
    <div class="invoice-overview">
        <div class="overview-item">
            <h3>Payment ID</h3>
            <strong><?= $history['ChargeHistory']['id'] ?></strong>
        </div>
        <div class="overview-item">
            <h3>Payment Date</h3>
            <strong><?= $history['ChargeHistory']['local_charge_date'] ?></strong>
        </div>
        <div class="overview-item">
            <h3><?= __('Team Name');?></h3>
            <strong><?= $history['Team']['name'] ?></strong>
        </div>
        <div class="overview-item">
            <h3><?= __('Billed To');?></h3>
            <p><?= $history['ChargeHistory']['total_with_currency'] ?> charged to card ending in <?= $history['CreditCard']['last4'] ?></p>
            <p><?= $history['PaymentSetting']['contact_person_email'] ?> <br /> <?= $history['PaymentSetting']['company_name'] ?></p>
        </div>
    </div>
    <div class="invoice-table">
        <table>
            <tbody>
                <th colspan="2">Type</th>
                <th><?= __('Term');?></th>
                <th><?= __('Amount');?></th>
                <tr>
                    <td>Monthly</td>
                    <td><?= $history['ChargeHistory']['charge_users'] ?> members</td>
                    <td><?= $history['ChargeHistory']['term'] ?></td>
                    <td><?= $history['ChargeHistory']['sub_total_with_currency'] ?></td>
                </tr>
                <tr>
                    <td colspan="3">Tax</td>
                    <td><?= $history['ChargeHistory']['tax_with_currency'] ?></td>
                </tr>
            </tbody>
        </table>
        <footer>
            <strong class="total-label">Total</strong>
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
            <?=__('This is only for your records, no payment is due. If you have any questions, please contact us at contact@goalous.com. Thank you for your business!');?>
        </footer>
    </div>
</article>
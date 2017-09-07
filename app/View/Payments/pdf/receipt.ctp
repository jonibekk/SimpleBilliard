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
            <h3><?= __('Invoice ID');?></h3>
            <strong>12345678</strong>
        </div>
        <div class="overview-item">
            <h3><?= __('Invoice Date');?></h3>
            <strong>Sept 30, 2017</strong>
        </div>
        <div class="overview-item">
            <h3><?= __('Team Name');?></h3>
            <strong>ISAO Corporation</strong>
        </div>
        <div class="overview-item">
            <h3><?= __('Billed To');?></h3>
            <p>$235.50 charged to card ending in 0000</p>
            <p>kikuchik@isao.co.jp <br /> ISAO Corporation</p>
        </div>
    </div>
    <div class="invoice-table">
        <header>
            Aug 1, 2017 - Sep 1, 2017
        </header>
        <table>
            <tbody>
                <th colspan="2">Type</th>
                <th><?= __('Time Period');?></th>
                <th><?= __('Amount');?></th>
                <tr>
                    <td><?= __('Monthly');?></td>
                    <td>100 members</td>
                    <td>Jul 1, 2017 - August 1, 2017</td>
                    <td>$1800.00</td>
                </tr>
                <tr>
                    <td colspan="3"><?=__('Tax');?></td>
                    <td>$180</td>
                </tr>
            </tbody>
        </table>
        <footer>
            <strong class="total-label"><?=__('Total');?></strong>
            <strong class="total-amount">$1890.00</strong>
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
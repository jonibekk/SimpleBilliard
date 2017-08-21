<?= $this->App->viewStartComment() ?>
<section class="panel paymentMethod">
    <div class="paymentMethod-inner">
        <select class="form-control mb_32px">
            <option><?=__("Subscription")?></option>
            <option><?=__("Invoice history")?></option>
            <option><?=__("Payment method")?>1</option>
            <option><?=__("Settings")?>1</option>
        </select>
        <h3 class=""><?= __("Credit Card")?></h3>
        <!-- canormal start-->
        <table class="paymentMethod-cc-table">
            <thead class="paymentMethod-cc-table-head">
            <tr>
                <td><?= __('Card');?></td>
                <td><?= __('Expire');?></td>
            </tr>
            </thead>
            <tbody class="paymentMethod-cc-table-body">
            <tr>
                <td>VISA *******1234</td>
                <td>
                    <span class="paymentMethod-cc-table-expireDate">Jan 31, 2018</span>
                    <a href="#" class="btn btn-primary pull-right"><?=__('Update Card');?></a>
                </td>
            </tr>
            </tbody>
        </table>
        <!-- normal end-->
        <!-- card expire start-->
        <table class="paymentMethod-cc-table">
            <thead class="paymentMethod-cc-table-head">
            <tr>
                <td><?= __('Card');?></td>
                <td><?= __('Expire');?></td>
            </tr>
            </thead>
            <tbody class="paymentMethod-cc-table-body">
            <tr>
                <td>VISA *******1234</td>
                <td>
                    <span class="paymentMethod-cc-table-expireDate mod-alert">Jan 31, 2018</span>
                    <a href="#" class="btn btn-primary pull-right"><?=__('Update Card');?></a>
                </td>
            </tr>
            </tbody>
        </table>
        <div class="has-error">
            <small class="help-block"><?=__("Card is expired. You must update card information.")?></small>
        </div>
        <!-- card expire end-->
    </div>
</section>
<?= $this->App->ViewEndComment() ?>


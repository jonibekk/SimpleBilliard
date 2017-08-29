<?= $this->App->viewStartComment() ?>
<section class="panel paymentMethod">
    <div class="paymentMethod-inner">
        <?= $this->element('Payment/method_select') ?>
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
                    <span class="paymentMethod-cc-table-expireDate  mr_8px">Jan 31, 2018</span>
                </td>
            </tr>
            </tbody>
        </table>
        <!-- normal end-->
        <!-- card expire start-->
<!--        <table class="paymentMethod-cc-table">-->
<!--            <thead class="paymentMethod-cc-table-head">-->
<!--            <tr>-->
<!--                <td>--><?//= __('Card');?><!--</td>-->
<!--                <td>--><?//= __('Expire');?><!--</td>-->
<!--            </tr>-->
<!--            </thead>-->
<!--            <tbody class="paymentMethod-cc-table-body">-->
<!--            <tr>-->
<!--                <td>VISA *******1234</td>-->
<!--                <td>-->
<!--                    <span class="paymentMethod-cc-table-expireDate mod-alert mr_8px">Jan 31, 2018</span>-->
<!--                    <a href="#" class="btn btn-primary">--><?//=__('Update Card');?><!--</a>-->
<!--                    </div>-->
<!--                </td>-->
<!--            </tr>-->
<!--            </tbody>-->
<!--        </table>-->
        <div class="has-error">
            <small class="help-block"><?=__("Card is expired. Please update your card's information.")?></small>
        </div>
        <!-- card expire end-->
    </div>
</section>
<?= $this->App->ViewEndComment() ?>


<?= $this->App->viewStartComment() ?>
<section
    class="panel payment">
    <div class="panel-container">
        <h3><?= __("Select Plan") ?></h3>
        <p><?= __('You have 187 active members. Please select the best plan for the number of members expected for your team.') ?></p>
        <table class="payment-table campaign-table">
            <thead>
                <tr>
                    <td><strong><?= __('Plan'); ?></strong><br /><?= __('max members');?></td>
                    <td><strong><?= __('Price'); ?></strong><br /><?= __('per month');?></td>
                    <td>&nbsp;</td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= __('500 members');?></td>
                    <td>&yen;250,000</td>
                    <td><a href="#" class="btn small"><?=__('Select');?></a></td>
                </tr>
                <tr>
                    <td><?= __('400 members');?></td>
                    <td>&yen;200,000</td>
                    <td><a href="#" class="btn small"><?=__('Select');?></a></td>
                </tr>
                <tr>
                    <td><?= __('300 members');?></td>
                    <td>&yen;150,000</td>
                    <td><a href="#" class="btn small"><?=__('Select');?></a></td>
                </tr>
                <tr>
                    <td><?= __('200 members');?></td>
                    <td>&yen;100,000</td>
                    <td><span class="fa fa-check success"></span></td>
                </tr>
                <tr>
                    <td><?= __('50 members');?></td>
                    <td>&yen;50,000</td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        <p><?= __('Larger plans available on request. All prices are without tax.');?></p>
    </div>
    <div class="panel-footer setting_pannel-footer">
        <a class="btn btn-link design-cancel bd-radius_4px" href="/payments/method">
            <?= __("Cancel") ?>
        </a>
        <input type="submit" class="btn btn-primary" value="<?= __("Update") ?>" disabled="disabled" />
    </div>
</section>
<?= $this->App->ViewEndComment() ?>

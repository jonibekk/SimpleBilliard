<?php
/**
 * @var array h($invoice
 */
?>
<?= $this->App->viewStartComment() ?>
<section class="panel payment company-info has-subnav">
    <?= $this->element('Payment/method_select') ?>
    <form class="form-horizontal" name="editInvoiceForm" id="editInvoiceForm" accept-charset="utf-8">
        <div class="panel-container">
            <h3><?= __("Billing Information") ?></h3>
            <div class="form-group">
                <label for="PaymentsCompanyName" class="circle-create-label"><?= __("Company Name") ?></label>
                <input
                    type="text" id="PaymentsCompanyName" name="company_name" value="<?= h($invoice['company_name']) ?>"
                    required class="form-control"
                    placeholder="<?= __("Colorkrew Inc.") ?>" maxlength="255">
            </div>
            <fieldset class="company-info-fieldset">
                <legend class="company-info-legend"><?= __("Company Address") ?></legend>
                <div class="form-group">
                    <label for="PaymentsCompanyPostCode" class="circle-create-label"><?= __("Post Code") ?></label>
                    <input type="tel" id="PaymentsCompanyPostCode" name="company_post_code"
                           value="<?= h($invoice['company_post_code']) ?>" required
                           class="form-control" placeholder="<?= __("12345 ") ?>" maxlength="16">
                </div>
                <div class="form-group">
                    <label for="PaymentsCompanyAddressRegion"
                           class="circle-create-label"><?= __("State/Province/Region") ?></label>
                    <input type="text"
                           id="company_region"
                           name="company_region" value="<?= h($invoice['company_region']) ?>" required
                           class="form-control"
                           placeholder="<?= __("California") ?>"
                           maxlength="255">
                </div>
                <div class="form-group">
                    <label for="PaymentsCompanyAddressCity"
                           class="circle-create-label"><?= __("City") ?></label>
                    <input type="text"
                           id="PaymentsCompanyAddressCity"
                           name="company_city" value="<?= h($invoice['company_city']) ?>" required
                           class="form-control"
                           placeholder="<?= __("Los Angeles") ?>"
                           maxlength="255">
                </div>
                <div class="form-group">
                    <label for="PaymentsCompanyAddressStreet"
                           class="circle-create-label"><?= __("Street") ?></label>
                    <input type="text"
                           id="PaymentsCompanyAddressStreet"
                           name="company_street"
                           value="<?= h($invoice['company_street']) ?>" required class="form-control"
                           placeholder="<?= __("1234 Street Name") ?>"
                           maxlength="255">
                </div>
            </fieldset>
            <fieldset class="company-info-fieldset">
                <legend class="company-info-legend"><?= __("Company Contact") ?></legend>
                <div class="form-group">
                    <label class="circle-create-label"><?= __("Name") ?></label>
                    <div class="flex">
                        <input type="text" id="PaymentsContactPersonLastName" name="contact_person_last_name"
                               value="<?= h($invoice['contact_person_last_name']) ?>" required
                               class="form-control   mr_8px"
                               placeholder="<?= __("Smith") ?>">
                        <input type="text"
                               id="PaymentsContactPersonFirstName"
                               name="contact_person_first_name"
                               value="<?= h($invoice['contact_person_first_name']) ?>" required
                               class="form-control  "
                               placeholder="<?= __("John") ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="circle-create-label"><?= __("Name Kana") ?></label>
                    <div class="flex">
                        <input type="text" id="PaymentsContactPersonLastNameKana"
                               name="contact_person_last_name_kana"
                               value="<?= h($invoice['contact_person_last_name_kana']) ?>"
                               required class="form-control   mr_8px"
                               placeholder="スズキ">
                        <input type="text" id="PaymentsContactPersonFirstNameKana"
                               name="contact_person_first_name_kana"
                               value="<?= h($invoice['contact_person_first_name_kana']) ?>" required
                               class="form-control  " placeholder="タロウ"></div>
                </div>
            </fieldset>
            <div class="form-group">
                <label for="PaymentsContactPersonEmail" class="circle-create-label"><?= __("Email") ?></label>
                <input
                    type="email" id="PaymentsContactPersonEmail" name="contact_person_email"
                    value="<?= h($invoice['contact_person_email']) ?>" required
                    class="form-control"
                    placeholder="name@company.co.jp" maxlength="255"></div>
            <div class="form-group">
                <label for="PaymentsContactPersonPhone"
                       class="circle-create-label"><?= __("Telephone") ?></label>
                <input type="tel"
                       id="PaymentsContactPersonPhone"
                       name="contact_person_tel"
                       value="<?= h($invoice['contact_person_tel']) ?>" required class="form-control"
                       placeholder="00000000000"
                       maxlength="255">
            </div>
        </div>
        <footer class="panel-footer setting_pannel-footer">
            <button class="btn btn-primary"><?= __("Update") ?></button>
        </footer>
    </form>
</section>
<?= $this->App->ViewEndComment() ?>


<?php
/**
 * @var array $setting
 */
?>
<?= $this->App->viewStartComment() ?>
<section class="panel company-info paymentMethod">
    <div class="form-group">
        <?= $this->element('Payment/method_select') ?>
    </div>
    <h3><?= __("Billing Information") ?></h3>
    <form class="form-horizontal" name="editPaySettingsForm" id="editPaySettingsForm" accept-charset="utf-8">
        <div class="form-group">
            <label for="PaymentsCompanyName" class="circle-create-label"><?= __("Company Name") ?></label>
            <input
                    type="text" id="PaymentsCompanyName" name="company_name" value="<?= $setting['company_name'] ?>"
                    required class="form-control"
                    placeholder="<?= __("ISAO Corporation") ?>" maxlength="255">
        </div>
        <fieldset class="company-info-fieldset">
            <legend class="company-info-legend"><?= __("Company Address") ?></legend>
            <div class="form-group">
                <label for="PaymentsCompanyPostCode" class="circle-create-label"><?= __("Country") ?></label>
                <?php echo $this->Form->input('current_team',
                    array(
                        'type'      => 'select',
                        'options'   => $countries,
                        'value'     => $setting['company_country'],
                        'id'        => 'PaymentsCompanyCountry',
                        'name'      => 'company_country',
                        'label'     => false,
                        'div'       => false,
                        'class'     => 'form-control',
                        'wrapInput' => false,
                        'form'      => 'editPaySettingsForm',
                    ))
                ?>
            </div>
            <div class="form-group">
                <label for="PaymentsCompanyPostCode" class="circle-create-label"><?= __("Post Code") ?></label>
                <input type="tel" id="PaymentsCompanyPostCode" name="company_post_code"
                       value="<?= $setting['company_post_code'] ?>" required
                       class="form-control" placeholder="<?= __("12345") ?>" maxlength="16">
            </div>
            <div class="form-group">
                <label for="PaymentsCompanyAddressRegion"
                       class="circle-create-label"><?= __("State/Province/Region") ?></label>
                <input type="text"
                       id="company_region"
                       name="company_region" value="<?= $setting['company_region'] ?>" required
                       class="form-control"
                       placeholder="<?= __("California") ?>"
                       maxlength="255">
            </div>
            <div class="form-group">
                <label for="PaymentsCompanyAddressCity"
                       class="circle-create-label"><?= __("City") ?></label>
                <input type="text"
                       id="PaymentsCompanyAddressCity"
                       name="company_city" value="<?= $setting['company_city'] ?>" required
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
                       value="<?= $setting['company_street'] ?>" required class="form-control"
                       placeholder="<?= __("1234 Street Name") ?>"
                       maxlength="255">
            </div>
            <div class="form-group">
                <label for="PaymentsCompanyTelephone"
                       class="circle-create-label"><?= __("Telephone") ?></label>
                <input type="tel"
                       id="PaymentsCompanyTelephone"
                       name="company_tel"
                       value="<?= $setting['company_tel'] ?>" required class="form-control"
                       placeholder="000-0000-0000"
                       maxlength="255">
            </div>
        </fieldset>
        <fieldset class="company-info-fieldset">
            <legend class="company-info-legend"><?= __("Company Contact") ?></legend>
            <div class="form-group">
                <label class="circle-create-label"><?= __("Name") ?></label>
                <div class="flex">
                    <input type="text" id="PaymentsContactPersonLastName" name="contact_person_last_name"
                           value="<?= $setting['contact_person_last_name'] ?>" required class="form-control   mr_8px"
                           placeholder="<?= __("Smith") ?>">
                    <input type="text"
                           id="PaymentsContactPersonFirstName"
                           name="contact_person_first_name"
                           value="<?= $setting['contact_person_first_name'] ?>" required
                           class="form-control  "
                           placeholder="<?= __("John") ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="circle-create-label"><?= __("Name Kana") ?></label>
                <div class="flex">
                    <input type="text" id="PaymentsContactPersonLastNameKana"
                           name="contact_person_last_name_kana" value="<?= $setting['contact_person_last_name_kana'] ?>"
                           required class="form-control   mr_8px"
                           placeholder="<?= __("スズキ") ?>">
                    <input type="text" id="PaymentsContactPersonFirstNameKana"
                           name="contact_person_first_name_kana"
                           value="<?= $setting['contact_person_first_name_kana'] ?>" required
                           class="form-control  " placeholder="<?= __("タロウ") ?>"></div>
            </div>
        </fieldset>
        <div class="form-group">
            <label for="PaymentsContactPersonEmail" class="circle-create-label"><?= __("Email") ?></label>
            <input
                    type="email" id="PaymentsContactPersonEmail" name="contact_person_email"
                    value="<?= $setting['contact_person_email'] ?>" required
                    class="form-control"
                    placeholder="<?= __("name@company.com") ?>" maxlength="255"></div>
        <div class="form-group">
            <label for="PaymentsContactPersonPhone"
                   class="circle-create-label"><?= __("Telephone") ?></label>
            <input type="tel"
                   id="PaymentsContactPersonPhone"
                   name="contact_person_tel"
                   value="<?= $setting['contact_person_tel'] ?>" required class="form-control"
                   placeholder="000-0000-0000"
                   maxlength="255">
        </div>
        <footer>
            <button class="btn btn-primary"><?= __("Update") ?></button>
        </footer>
    </form>
</section>
<?= $this->App->ViewEndComment() ?>

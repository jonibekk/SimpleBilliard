<?php
/**
 * @var array $setting
 */
?>
<?= $this->App->viewStartComment() ?>
<section class="panel company-info paymentMethod has-subnav">
    <?= $this->element('Payment/method_select') ?>
    <h3><?= __("Company Information") ?></h3>
    <form class="form-horizontal" name="editPaySettingsForm" id="editPaySettingsForm" accept-charset="utf-8">
        <input type="hidden" id="editPaySettingsType" value="<?= $setting['type'] ?>"/>
        <div class="form-group">
            <label for="company_name" class="circle-create-label"><?= __("Company Name") ?></label>
            <input type="text" id="company_name" name="company_name" value="<?= $setting['company_name'] ?>"
                    required class="form-control"
                    placeholder="<?= __("ISAO Corporation") ?>" maxlength="255">
        </div>
        <fieldset class="company-info-fieldset">
            <legend class="company-info-legend"><?= __("Company Address") ?></legend>
            <div class="form-group">
                <label for="PaymentsCompanyPostCode" class="circle-create-label"><?= __("Country") ?></label>
                <?= $countries[$setting['company_country']] ?>
            </div>
            <div class="form-group">
                <label for="company_post_code" class="circle-create-label"><?= __("Post Code") ?></label>
                <input type="tel" id="company_post_code" name="company_post_code"
                       value="<?= $setting['company_post_code'] ?>" required
                       class="form-control" placeholder="<?= __("12345") ?>" maxlength="16">
            </div>
            <div class="form-group">
                <label for="company_region"
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
                       id="company_city"
                       name="company_city" value="<?= $setting['company_city'] ?>" required
                       class="form-control"
                       placeholder="<?= __("Los Angeles") ?>"
                       maxlength="255">
            </div>
            <div class="form-group">
                <label for="company_street"
                       class="circle-create-label"><?= __("Street") ?></label>
                <input type="text"
                       id="company_street"
                       name="company_street"
                       value="<?= $setting['company_street'] ?>" required class="form-control"
                       placeholder="<?= __("1234 Street Name") ?>"
                       maxlength="255">
            </div>
        </fieldset>
        <fieldset class="company-info-fieldset">
            <legend class="company-info-legend"><?= __("Company Contact") ?></legend>
            <div class="form-group">
                <div class="flex">
                    <div class="flex-extend mr_8px">
                        <input type="text" id="PaymentsContactPersonLastName" name="contact_person_last_name"
                               value="<?= $setting['contact_person_last_name'] ?>" required
                               class="form-control"
                               placeholder="<?= __("Smith") ?>">
                    </div>
                    <div class="flex-extend">
                        <input type="text"
                               id="PaymentsContactPersonFirstName"
                               name="contact_person_first_name"
                               value="<?= $setting['contact_person_first_name'] ?>" required
                               class="form-control  "
                               placeholder="<?= __("John") ?>">
                    </div>
                </div>
            </div>
            <?php if ((int)$setting['type'] === Goalous\Model\Enum\PaymentSetting\Type::INVOICE): ?>

                <div class="form-group">
                    <label class="circle-create-label"><?= __("Name Kana") ?></label>
                    <div class="flex">
                        <div class="flex-extend mr_8px">
                            <input type="text" id="PaymentsContactPersonLastNameKana"
                                   name="contact_person_last_name_kana"
                                   value="<?= $setting['contact_person_last_name_kana'] ?>"
                                   required class="form-control"
                                   placeholder="<?= __("スズキ") ?>">
                        </div>
                        <div class="flex-extend">
                            <input type="text" id="PaymentsContactPersonFirstNameKana"
                                   name="contact_person_first_name_kana"
                                   value="<?= $setting['contact_person_first_name_kana'] ?>" required
                                   class="form-control  " placeholder="<?= __("タロウ") ?>">
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </fieldset>
        <div class="form-group">
            <label for="contact_person_email" class="circle-create-label"><?= __("Email") ?></label>
            <input
                    type="email" id="PaymentsContactPersonEmail" name="contact_person_email"
                    value="<?= $setting['contact_person_email'] ?>" required
                    class="form-control"
                    placeholder="<?= __("name@company.com") ?>" maxlength="255"></div>
        <div class="form-group">
            <label for="contact_person_tel"
                   class="circle-create-label"><?= __("Telephone") ?></label>
            <input type="tel"
                   id="contact_person_tel"
                   name="contact_person_tel"
                   value="<?= $setting['contact_person_tel'] ?>" required class="form-control"
                   placeholder="000-0000-0000"
                   maxlength="255">
        </div>
        <footer>
            <button class="btn btn-primary" id="editPaySettingsSubmitBtn">
                <?= __("Update") ?>
            </button>
        </footer>
    </form>
</section>
<?= $this->App->ViewEndComment() ?>

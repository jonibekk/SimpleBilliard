<?= $this->App->viewStartComment() ?>
<section class="panel company-info paymentMethod">
    <div class="form-group">
        <select class="form-control mb_32px">
            <option><?= __("Subscription") ?></option>
            <option><?= __("Invoice history") ?></option>
            <option><?= __("Payment method") ?>1</option>
            <option><?= __("Settings") ?>1</option>
        </select>
    </div>
    <h3>請求先情報</h3>
    <form class="form-horizontal" name="" id="" accept-charset="utf-8">
        <div class="form-group">
            <label for="PaymentsCompanyName" class="circle-create-label">会社名</label>
            <input
                type="text" id="PaymentsCompanyName" name="company_name" value="" class="form-control"
                placeholder="株式会社ISAO" maxlength="255"></div>
        <fieldset class="company-info-fieldset">
            <legend class="company-info-legend">会社住所</legend>
            <div class="form-group">
                <label for="PaymentsCompanyPostCode" class="circle-create-label">Post
                    Code</label>
                <input type="tel" id="PaymentsCompanyPostCode" name="company_post_code" value=""
                       class="form-control" placeholder="1100053" maxlength="16">
            </div>
            <div class="form-group">
                <label for="PaymentsCompanyAddressRegion"
                       class="circle-create-label">都道府県</label>
                <input type="text"
                       id="PaymentsCompanyAddressRegion"
                       name="company_region" value=""
                       class="form-control"
                       placeholder="東京都"
                       maxlength="255">
            </div>
            <div class="form-group">
                <label for="PaymentsCompanyAddressCity"
                       class="circle-create-label">市区町村</label>
                <input type="text"
                       id="PaymentsCompanyAddressCity"
                       name="company_city" value=""
                       class="form-control"
                       placeholder="台東区浅草橋5丁目"
                       maxlength="255">
            </div>
            <div class="form-group">
                <label for="PaymentsCompanyAddressStreet"
                       class="circle-create-label">建物名など</label>
                <input type="text"
                       id="PaymentsCompanyAddressStreet"
                       name="company_street"
                       value="" class="form-control"
                       placeholder="20番8号 CSタワー7階"
                       maxlength="255">
            </div>
        </fieldset>
        <fieldset class="company-info-fieldset">
            <legend class="company-info-legend">会社連絡先</legend>
            <div class="form-group">
                <label class="circle-create-label">名前</label>
                <div class="flex">
                    <input type="text" id="PaymentsContactPersonLastName" name="contact_person_last_name"
                           value="" class="form-control   mr_8px" placeholder="例) 鈴木">
                    <input type="text"
                           id="PaymentsContactPersonFirstName"
                           name="contact_person_first_name"
                           value=""
                           class="form-control  "
                           placeholder="例) 太郎">
                </div>
            </div>
            <div class="form-group">
                <label class="circle-create-label">名前(カナ)</label>
                <div class="flex">
                    <input type="text" id="PaymentsContactPersonLastNameKana"
                           name="contact_person_last_name_kana" value="" class="form-control   mr_8px"
                           placeholder="スズキ">
                    <input type="text" id="PaymentsContactPersonFirstNameKana"
                           name="contact_person_first_name_kana" value=""
                           class="form-control  " placeholder="タロウ"></div>
            </div>
        </fieldset>
        <div class="form-group">
            <label for="PaymentsContactPersonEmail" class="circle-create-label">Email</label>
            <input
                type="email" id="PaymentsContactPersonEmail" name="contact_person_email" value="" class="form-control"
                placeholder="name@company.co.jp" maxlength="255"></div>
        <div class="form-group">
            <label for="PaymentsContactPersonPhone"
                   class="circle-create-label">Telephone</label>
            <input type="tel"
                   id="PaymentsContactPersonPhone"
                   name="contact_person_tel"
                   value="" class="form-control"
                   placeholder="000-0000-0000"
                   maxlength="255">
        </div>
        <footer>
            <button class="btn btn-primary">更新</button>
        </footer>
    </form>
</section>
<?= $this->App->ViewEndComment() ?>


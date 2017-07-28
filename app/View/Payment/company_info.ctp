<section class="panel company-info">
    <h3><?= __('Enter Company Information')?></h3>
    <?php
        echo $this->Form->create('Payments', [
            'url'           => ['controller' => 'payments', 'action' => 'addCompanyInfo'],
            'inputDefaults' => [
                'div'       => 'form-group',
                'label'     => [
                    'class' => 'circle-create-label'
                ],
                'wrapInput' => false,
                'class'     => 'form-control',
            ],
            'class'         => 'form-horizontal',
            'name'            => 'addCompanyInfo',
        ]);
        echo $this->Form->input('company_name',[
            'label'                        => __("Company Name"),
            'placeholder'                  => __("ISAO Corporation"),
            "data-bv-notempty-message"     => __("Input is required."),
            'type'                         => 'text',
            'data-bv-stringlength'         => 'true',
            'data-bv-stringlength-max'     => 255,
            'data-bv-stringlength-message' => __("It's over limit characters (%s).", 255),
        ]);
        ?>
    <fieldset class="company-info-fieldset">
        <legend class="company-info-legend"><?= __('Company Address');?></legend>
        <?php
        echo $this->Form->input('company_address',[
            'label'                        => __("Street"),
            'placeholder'                  => __("1234 Street Name"),
            'type'                         => 'text',
            "data-bv-notempty-message"     => __("Input is required."),
            'data-bv-stringlength'         => 'true',
            'data-bv-stringlength-max'     => 255,
            'data-bv-stringlength-message' => __("It's over limit characters (%s).", 255),
        ]);
        echo $this->Form->input('company_address',[
            'label'                        => __("State/Province/Region"),
            'placeholder'                  => __("California"),
            'type'                         => 'text',
            "data-bv-notempty-message"     => __("Input is required."),
            'data-bv-stringlength'         => 'true',
            'data-bv-stringlength-max'     => 255,
            'data-bv-stringlength-message' => __("It's over limit characters (%s).", 255),
        ]);
        echo $this->Form->input('company_address',[
            'label'                        => __("Postal Code"),
            'placeholder'                  => __("12345"),
            'type'                         => 'tel',
            "data-bv-notempty-message"     => __("Input is required."),
            'data-bv-stringlength'         => 'true',
            'data-bv-stringlength-max'     => 255,
            'data-bv-stringlength-message' => __("It's over limit characters (%s).", 255),
        ]);
        echo $this->Form->input('company_address',[
            'label'                        => __("Country"),
            'placeholder'                  => __("USA"),
            'type'                         => 'text',
            "data-bv-notempty-message"     => __("Input is required."),
            'data-bv-stringlength'         => 'true',
            'data-bv-stringlength-max'     => 255,
            'data-bv-stringlength-message' => __("It's over limit characters (%s).", 255),
        ]);
        ?>
    </fieldset>
    <fieldset class="company-info-fieldset">
        <legend class="company-info-legend"><?= __('Company Contact');?></legend>
        <?php
            echo $this->Form->input('contact_name_first',[
                'label'                        => __("First Name"),
                'placeholder'                  => __("John"),
                'type'                         => 'text',
                "data-bv-notempty-message"     => __("Input is required."),
                'data-bv-stringlength'         => 'true',
                'data-bv-stringlength-max'     => 255,
                'data-bv-stringlength-message' => __("It's over limit characters (%s).", 255),
            ]);
            echo $this->Form->input('contact_name_first',[
                'label'                        => __("Last Name"),
                'placeholder'                  => __("Smith"),
                'type'                         => 'text',
                "data-bv-notempty-message"     => __("Input is required."),
                'data-bv-stringlength'         => 'true',
                'data-bv-stringlength-max'     => 255,
                'data-bv-stringlength-message' => __("It's over limit characters (%s).", 255),
            ]);
        ?>
    </fieldset>
    <?php
        echo $this->Form->input('contact_email',[
            'label'                        => __("Contact Email"),
            'placeholder'                  => __("name@company.com"),
            'type'                         => 'email',
            "data-bv-notempty-message"     => __("Input is required."),
            'data-bv-stringlength'         => 'true',
            'data-bv-stringlength-max'     => 255,
            'data-bv-stringlength-message' => __("It's over limit characters (%s).", 255),
        ]);
        echo $this->Form->input('company_phone',[
            'label'                        => __("Company Telephone"),
            'placeholder'                  => __("ISAO Corporation"),
            'type'                         => 'tel',
            "data-bv-notempty-message"     => __("Input is required."),
            'data-bv-stringlength'         => 'true',
            'data-bv-stringlength-max'     => 255,
            'data-bv-stringlength-message' => __("It's over limit characters (%s).", 255),
        ]);
    ?>
    <div class="panel-footer setting_pannel-footer">
        <a class="btn btn-link design-cancel bd-radius_4px" href="/Payment/">
            <?= __("Cancel") ?>
        </a>
        <input type="submit" class="btn btn-primary" value="Next" />
    </div>
    <?php
            echo $this->Form->end(); 
    ?>
</section>
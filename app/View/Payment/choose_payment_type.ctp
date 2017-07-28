<section class="panel choose-payment">
    <div class="panel-container">
        <h3><?= __('Select Country Location')?></h3>
        <?php
        $countryList = ["Aruba"=>"AW","Afghanistan"=>"AF","Angola"=>"AO","Anguilla"=>"AI","Albania"=>"AL","Andorra"=>"AD","Netherlands Antilles"=>"AN","United Arab Emirates"=>"AE","Argentina"=>"AR","Armenia"=>"AM","American Samoa"=>"AS","Antarctica"=>"AQ","French Southern territories"=>"TF","Antigua and Barbuda"=>"AG","Australia"=>"AU","Austria"=>"AT","Azerbaijan"=>"AZ","Burundi"=>"BI","Belgium"=>"BE","Benin"=>"BJ","Burkina Faso"=>"BF","Bangladesh"=>"BD","Bulgaria"=>"BG","Bahrain"=>"BH","Bahamas"=>"BS","Bosnia and Herzegovina"=>"BA","Belarus"=>"BY","Belize"=>"BZ","Bermuda"=>"BM","Bolivia"=>"BO","Brazil"=>"BR","Barbados"=>"BB","Brunei"=>"BN","Bhutan"=>"BT","Bouvet Island"=>"BV","Botswana"=>"BW","Central African Republic"=>"CF","Canada"=>"CA","Cocos (Keeling) Islands"=>"CC","Switzerland"=>"CH","Chile"=>"CL","China"=>"CN","Ivory Coast"=>"CI","Cameroon"=>"CM","Congo, The Democratic Republic of the"=>"CD","Congo"=>"CG","Cook Islands"=>"CK","Colombia"=>"CO","Comoros"=>"KM","Cape Verde"=>"CV","Costa Rica"=>"CR","Cuba"=>"CU","Christmas Island"=>"CX","Cayman Islands"=>"KY","Cyprus"=>"CY","Czech Republic"=>"CZ","Germany"=>"DE","Djibouti"=>"DJ","Dominica"=>"DM","Denmark"=>"DK","Dominican Republic"=>"DO","Algeria"=>"DZ","Ecuador"=>"EC","Egypt"=>"EG","Eritrea"=>"ER","Western Sahara"=>"EH","Spain"=>"ES","Estonia"=>"EE","Ethiopia"=>"ET","Finland"=>"FI","Fiji Islands"=>"FJ","Falkland Islands"=>"FK","France"=>"FR","Faroe Islands"=>"FO","Micronesia, Federated States of"=>"FM","Gabon"=>"GA","United Kingdom"=>"GB","Georgia"=>"GE","Ghana"=>"GH","Gibraltar"=>"GI","Guinea"=>"GN","Guadeloupe"=>"GP","Gambia"=>"GM","Guinea-Bissau"=>"GW","Equatorial Guinea"=>"GQ","Greece"=>"GR","Grenada"=>"GD","Greenland"=>"GL","Guatemala"=>"GT","French Guiana"=>"GF","Guam"=>"GU","Guyana"=>"GY","Hong Kong"=>"HK","Heard Island and McDonald Islands"=>"HM","Honduras"=>"HN","Croatia"=>"HR","Haiti"=>"HT","Hungary"=>"HU","Indonesia"=>"ID","India"=>"IN","British Indian Ocean Territory"=>"IO","Ireland"=>"IE","Iran"=>"IR","Iraq"=>"IQ","Iceland"=>"IS","Israel"=>"IL","Italy"=>"IT","Jamaica"=>"JM","Jordan"=>"JO","Japan"=>"JP","Kazakstan"=>"KZ","Kenya"=>"KE","Kyrgyzstan"=>"KG","Cambodia"=>"KH","Kiribati"=>"KI","Saint Kitts and Nevis"=>"KN","South Korea"=>"KR","Kuwait"=>"KW","Laos"=>"LA","Lebanon"=>"LB","Liberia"=>"LR","Libyan Arab Jamahiriya"=>"LY","Saint Lucia"=>"LC","Liechtenstein"=>"LI","Sri Lanka"=>"LK","Lesotho"=>"LS","Lithuania"=>"LT","Luxembourg"=>"LU","Latvia"=>"LV","Macao"=>"MO","Morocco"=>"MA","Monaco"=>"MC","Moldova"=>"MD","Madagascar"=>"MG","Maldives"=>"MV","Mexico"=>"MX","Marshall Islands"=>"MH","Macedonia"=>"MK","Mali"=>"ML","Malta"=>"MT","Myanmar"=>"MM","Mongolia"=>"MN","Northern Mariana Islands"=>"MP","Mozambique"=>"MZ","Mauritania"=>"MR","Montserrat"=>"MS","Martinique"=>"MQ","Mauritius"=>"MU","Malawi"=>"MW","Malaysia"=>"MY","Mayotte"=>"YT","Namibia"=>"NA","New Caledonia"=>"NC","Niger"=>"NE","Norfolk Island"=>"NF","Nigeria"=>"NG","Nicaragua"=>"NI","Niue"=>"NU","Netherlands"=>"NL","Norway"=>"NO","Nepal"=>"NP","Nauru"=>"NR","New Zealand"=>"NZ","Oman"=>"OM","Pakistan"=>"PK","Panama"=>"PA","Pitcairn"=>"PN","Peru"=>"PE","Philippines"=>"PH","Palau"=>"PW","Papua New Guinea"=>"PG","Poland"=>"PL","Puerto Rico"=>"PR","North Korea"=>"KP","Portugal"=>"PT","Paraguay"=>"PY","Palestine"=>"PS","French Polynesia"=>"PF","Qatar"=>"QA","Reunion"=>"RE","Romania"=>"RO","Russian Federation"=>"RU","Rwanda"=>"RW","Saudi Arabia"=>"SA","Sudan"=>"SD","Senegal"=>"SN","Singapore"=>"SG","South Georgia and the South Sandwich Islands"=>"GS","Saint Helena"=>"SH","Svalbard and Jan Mayen"=>"SJ","Solomon Islands"=>"SB","Sierra Leone"=>"SL","El Salvador"=>"SV","San Marino"=>"SM","Somalia"=>"SO","Saint Pierre and Miquelon"=>"PM","Sao Tome and Principe"=>"ST","Suriname"=>"SR","Slovakia"=>"SK","Slovenia"=>"SI","Sweden"=>"SE","Swaziland"=>"SZ","Seychelles"=>"SC","Syria"=>"SY","Turks and Caicos Islands"=>"TC","Chad"=>"TD","Togo"=>"TG","Thailand"=>"TH","Tajikistan"=>"TJ","Tokelau"=>"TK","Turkmenistan"=>"TM","East Timor"=>"TP","Tonga"=>"TO","Trinidad and Tobago"=>"TT","Tunisia"=>"TN","Turkey"=>"TR","Tuvalu"=>"TV","Taiwan"=>"TW","Tanzania"=>"TZ","Uganda"=>"UG","Ukraine"=>"UA","United States Minor Outlying Islands"=>"UM","Uruguay"=>"UY","United States"=>"US","Uzbekistan"=>"UZ","Holy See (Vatican City State)"=>"VA","Saint Vincent and the Grenadines"=>"VC","Venezuela"=>"VE","Virgin Islands,British"=>"VG","Virgin Islands,U.S."=>"VI","Vietnam"=>"VN","Vanuatu"=>"VU","Wallis and Futuna"=>"WF","Samoa"=>"WS","Yemen"=>"YE","Yugoslavia"=>"YU","South Africa"=>"ZA","Zambia"=>"ZM","Zimbabwe"=>"ZW"];
        ?>
        <form action="#" class="form-horizontal" name="companyLocation">
            <select name="country" id="" class="form-control setting_input-design">
                <option value="false" selected><?= __('Choose Country');?></option>
                <?php
                    foreach($countryList as $country => $code){
                        echo "<option value='".$code."'>".$country."</option>";
                    }
                ?>
            </select>
            <input type="submit" value="Submit" class="btn btn-primary" disabled="disabled">
        </form>
        <div class="payment-options">
            <div class="payment-option-container">
                <h3><?= __('Select Payment Method')?></h3>
                <div class="payment-option" onClick="window.location='#'">
                    <h4><?= __('Credit Card') ?></h4>
                    <i class="fa fa-credit-card"></i>
                    <p><?= __("Use a credit card to setup automatic, reoccuring payments for your Goalous team.") ?></p>
                    <a href="/Payment/enterCompanyInfo"><?= __('Setup') ?></a>
                </div>
                <div class="payment-option upcoming">
                    <h4><?= __('Invoice') ?></h4>
                    <i class="fa fa-leaf"></i>
                    <p><?= __("Setup a monthly invoice with Goalous.") ?></p>
                    <p class="coming-soon"><?= __('Coming Soon') ?></a>
                </div>
            </div>
        </div>
    </div>
</section>
<?php
App::uses('ValidationErrorI18nBehavior', 'Cakeplus.Model/Behavior');

/**
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 2014/05/22
 * Time: 1:01
 */
class ExtValidationErrorI18nBehavior extends ValidationErrorI18nBehavior
{
    function beforeValidate(Model $model, $options = [])
    {
        //ここに全てのエラーメッセージを記載する
        $error_messages = array(
            'notEmpty'              => __("Input is required."),
            'notBlank'              => __("Input is required."),
            'requiredCaseExistUnit' => __("Input is required."),
            'numeric'               => __("Only Numeric characters are allowed."),
            'isAlphabetOnly'        => __("It includes restricted strings. Allowed characters are only alphanumeric, space and apostrophe."),
            'isString'              => __("Invalid value"),
            'email'                 => __("Email address is incorrect."),
            'notBlankCheckbox'      => __("Need to agree our terms."),
            'passwordSameCheck'     => __("Both of passwords are not same."),
            'passwordCheck'         => __("Password is incorrect."),
            'minLength'             => __('At least %2$d characters is required.'),
            'maxLength'             => __('%2$d or less than %2$d characters are allowed.'),
            'emailIsUnique'         => __("This email address has already been used. Use another email address."),
            'image_max_size'        => __("The file is over limit size."),
            'imageMinWidthHeight'   => __("The Image does not have enough vertical or horizontal size."),
            'image_type'            => __("Failed to upload. jpg, png and gif are allowed."),
            'emailsCheck'           => __("Some email addresses are incorrect."),
            'passwordPolicy'        => __('Please mix of numbers and alphanumeric.'),
            'userNameChar'          => __("It includes restricted strings. Allowed characters are only alphanumeric, space and apostrophe."),
            'inList'                => __("Invalid value"),
            'dateYmd'               => __('Enter a valid date in YYYY/MM/DD format.'),
            'checkRangeTerm'        => __("Input in the term."),
            'isUnique'              => __('Data is duplicated.'),
            'validateExistKr'       => __("Please select"),
            'decimal'       => __("Invalid value"),
        );
        $this->setErrorMessageI18n($model, $error_messages, false);
        $this->replaceValidationErrorMessagesI18n($model);
        parent::beforeValidate($model, $options);
        return true;
    }

    /**
     * Replace validation error messages for i18n
     *
     * @access public
     */
    function replaceValidationErrorMessagesI18n(Model $model)
    {
        $this->setErrorMessageI18n($model, null, false);

        foreach ($model->validate as $fieldname => $ruleSet) {
            foreach ($ruleSet as $rule => $rule_info) {

                $rule_option = array();
                if (!empty($model->validate[$fieldname][$rule]['rule'])) {
                    $rule_option = $model->validate[$fieldname][$rule]['rule'];
                }

                $error_message_list = $this->_getErrorMessageI18n();
                $error_message = (array_key_exists($rule, $error_message_list) ? $error_message_list[$rule] : null);

                if (!empty($error_message) &&
                    (!isset($model->validate[$fieldname][$rule]['message']) || empty($model->validate[$fieldname][$rule]['message']))
                ) {
                    $model->validate[$fieldname][$rule]['message'] = vsprintf($error_message, $rule_option);
                } elseif (!empty($model->validate[$fieldname][$rule]['message'])) {
                    $model->validate[$fieldname][$rule]['message'] = __($model->validate[$fieldname][$rule]['message']);
                }

                if ($this->_withFieldName && !empty($model->validate[$fieldname][$rule]['message'])) {
                    $model->validate[$fieldname][$rule]['message'] = __($fieldname) . ' : ' . $model->validate[$fieldname][$rule]['message'];
                }
            }
        }
    }

}

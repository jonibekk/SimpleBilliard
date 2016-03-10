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
            'notEmpty'          => __("Input is required."),
            'isAlphabetOnly'    => __("アルファベットのみで入力してください。"),
            'isString'          => __("不正な入力値です。"),
            'email'             => __("メールアドレスが正しくありません。"),
            'notBlankCheckbox'  => __("利用規約に同意してください。"),
            'passwordSameCheck' => __("パスワードが一致しません。"),
            'passwordCheck'     => __("パスワードが間違っています。"),
            'minLength'         => __('%2$d文字以上で入力してください。'),
            'maxLength'         => __('%2$d文字以下で入力してください。'),
            'emailIsUnique'     => __("このメールアドレスは既に使用されています。他のメールアドレスを指定してください。"),
            'image_max_size'    => __("ファイルサイズが上限を超えています。"),
            'image_type'        => __("画像をアップロードできませんでした。JPG、PNG、GIFのいずれかの形式で保存してください。"),
            'emailsCheck'       => __("幾つかのメールアドレスが正しくありません。"),
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
                }
                elseif (!empty($model->validate[$fieldname][$rule]['message'])) {
                    $model->validate[$fieldname][$rule]['message'] = __($model->validate[$fieldname][$rule]['message']);
                }

                if ($this->_withFieldName && !empty($model->validate[$fieldname][$rule]['message'])) {
                    $model->validate[$fieldname][$rule]['message'] = __($fieldname) . ' : ' . $model->validate[$fieldname][$rule]['message'];
                }
            }
        }
    }

}
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
            'notEmpty'          => __d('validate', "入力必須項目です。"),
            'isAlphabetOnly'    => __d('validate', "アルファベットのみで入力してください。"),
            'email'             => __d('validate', "メールアドレスが正しくありません。"),
            'notBlankCheckbox'  => __d('validate', "利用規約に同意してください。"),
            'passwordSameCheck' => __d('validate', "パスワードが一致しません。"),
            'minLength'         => __d('validate', '%2$d文字以上で入力してください。'),
            'emailIsUnique'     => __d('validate', "このメールアドレスは既に使用されています。他のメールアドレスを指定してください。"),
            'isEmailExists'     => __d('validate', "このメールアドレスは登録されていません。ご確認ください。"),
            'image_max_size'    => __d('validate', "ファイルサイズが上限を超えています。"),
        );
        $this->setErrorMessageI18n($model, $error_messages, false);
        $this->replaceValidationErrorMessagesI18n($model);
        parent::beforeValidate($model, $options);
        return true;
    }
}
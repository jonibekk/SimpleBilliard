<?php
App::uses('Model', 'Model');

/**
 * Application model for Cake.
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppModel extends Model
{

    var $actsAs = [
        'Cakeplus.AddValidationRule',
        'Cakeplus.ValidationErrorI18n',
        'Cakeplus.ValidationPatterns',
    ];

    //全てのモデルでデフォルトで再起的にjoinするのをやめる。個別に指定する。
    public $recursive = -1;

    function beforeValidate($options = [])
    {
        //ここに全てのエラーメッセージを記載する
        $error_messages = array(
            'notEmpty'          => __d('validate', "入力必須項目です。"),
            'isAlphabetOnly'    => __d('validate', "アルファベットのみで入力してください。"),
            'email'             => __d('validate', "メールアドレスが正しくありません。"),
            'notBlankCheckbox'  => __d('validate', "利用規約に同意してください。"),
            'passwordSameCheck' => __d('validate', "パスワードが一致しません。"),
            'minLength'         => __d('validate', '%2$d文字以上で入力してください。'),
        );
        /** @noinspection PhpUndefinedMethodInspection */
        $this->setErrorMessageI18n($error_messages, false);
        /** @noinspection PhpUndefinedMethodInspection */
        $this->replaceValidationErrorMessagesI18n();
        parent::beforeValidate($options);
        return true;
    }

    /**
     * アルファベットのみかどうかチェックするバリデーションルール
     */
    function isAlphabetOnly($field = [])
    {
        if (is_array($field)) {
            foreach ($field as $value) {
                if (preg_match("/^([a-zA-Z])*$/u", $value)) {
                    return true;
                }
                else {
                    return false;
                }
            }
        }
        else {
            if (preg_match("/^([a-zA-Z])*$/u", $field)) {
                return true;
            }
            else {
                return false;
            }
        }
        return false;
    }

    /**
     * パスワードの同一性チェックをするバリデーションルール
     *
     * @param $value
     * @param $field_name
     *
     * @return bool
     */
    public function passwordSameCheck($value, $field_name)
    {
        $v1 = array_shift($value);
        $v2 = $this->data[$this->name][$field_name];
        return $v1 == $v2;
    }
}

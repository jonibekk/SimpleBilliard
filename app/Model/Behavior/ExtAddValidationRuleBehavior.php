<?php
App::uses('AddValidationRuleBehavior', 'Cakeplus.Model/Behavior');

/**
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 2014/05/22
 * Time: 1:01
 */
class ExtAddValidationRuleBehavior extends AddValidationRuleBehavior
{
    /**
     * アルファベットのみかどうかチェックするバリデーションルール
     *
     * @param Model $Model
     * @param array $field
     *
     * @return bool
     */
    function isAlphabetOnly(
        /** @noinspection PhpUnusedParameterInspection */
        Model $Model,
        $field = []
    ) {
        if (is_array($field)) {
            foreach ($field as $value) {
                if (preg_match("/^([a-zA-Z])*$/u", $value)) {
                    return true;
                } else {
                    return false;
                }
            }
        } else {
            if (preg_match("/^([a-zA-Z])*$/u", $field)) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }

    /**
     * Check if value exist in enum values
     *
     * @param Model $Model
     * @param       $value
     * @param       $enumClass
     *
     * @return bool
     * @internal param array $field
     */
    function inEnumList(Model $Model, $value, $enumClass)
    {
        $value = array_shift($value);
        $constants = call_user_func([$enumClass, 'toArray']);
        return in_array($value, $constants);
    }

    /**
     * パスワードの同一性チェックをするバリデーションルール
     *
     * @param Model $Model
     * @param       $value
     * @param       $field_name
     *
     * @return bool
     */
    public function passwordSameCheck(Model $Model, $value, $field_name)
    {
        $v1 = array_shift($value);

        $v2 = $Model->data[$Model->name][$field_name];
        return $v1 == $v2;
    }

    /**
     * カンマ、改行区切りのメールアドレスリストのValidationチェック
     *
     * @param Model $Model
     * @param       $value
     *
     * @return bool
     */
    public function emailsCheck(
        /** @noinspection PhpUnusedParameterInspection */
        Model $Model,
        $value
    ) {
        App::import('Core', 'Validation');
        foreach ($value as $v) {
            //一行ずつ処理
            $cr = array("\r\n", "\r"); // 改行コード置換用配列を作成しておく

            $v = trim($v); // 文頭文末の空白を削除

            // 改行コードを統一
            //str_replace ("検索文字列", "置換え文字列", "対象文字列");
            $v = str_replace($cr, "\n", $v);

            //メアドのカウント
            $count = 0;

            //改行コードで分割（結果は配列に入る）
            $lines_array = explode("\n", $v);
            //一行ずつ処理
            foreach ($lines_array as $line) {
                //カンマで分割
                $emails = explode(",", $line);
                //メールアドレス毎に処理
                foreach ($emails as $email) {
                    //全角スペースを除去
                    $email = preg_replace('/　/', ' ', $email);
                    //前後スペースを除去
                    $email = trim($email);
                    //空行はスキップ
                    if (empty($email)) {
                        continue;
                    }
                    if (!Validation::email($email)) {
                        return false;
                    }
                    $count++;
                }
            }
            //メールアドレスが一つもない場合はfalse
            if ($count === 0) {
                return false;
            }
        }
        return true;
    }

    function isString(
        /** @noinspection PhpUnusedParameterInspection */
        Model $Model,
        $value
    ) {
        $value = array_values($value);

        $value = $value[0];
        return is_string($value);
    }

    function isOnOrOff(
        /** @noinspection PhpUnusedParameterInspection */
        Model $Model,
        $check
    ) {
        $value = array_values($check);
        $value = $value[0];
        return strtolower($value) == 'on' || strtolower($value) == 'off';
    }

    function phoneNo(
        /** @noinspection PhpUnusedParameterInspection */
        Model $Model,
        $check
    ) {
        $value = array_values($check);
        $value = $value[0];
        return preg_match('/^[0-9-\(\)+]+$/', $value);
    }

    function isAllOrNothing(
        /** @noinspection PhpUnusedParameterInspection */
        Model $Model,
        $check,
        $compare_fields
    ) {
        $exists = false;
        $not_exists = false;
        foreach ($compare_fields as $field) {
            if (empty($Model->data[$Model->alias][$field])) {
                $not_exists = true;
            } else {
                $exists = true;
            }

        }
        return $exists !== $not_exists;
    }

    function isAlignLeft(
        /** @noinspection PhpUnusedParameterInspection */
        Model $Model,
        $array
    ) {
        $array = array_values($array);
        $array = $array[0];
        //first remove empty data
        foreach ($array as $k => $v) {
            if (!$v) {
                unset($array[$k]);
            }
        }

        if (empty($array)) {
            return true;
        }

        //if toothless then return false
        $expect_k = 1;
        foreach ($array as $k => $v) {
            if ($k !== $expect_k) {
                return false;
            }
            $expect_k++;
        }
        return true;
    }

    function maxLengthArray(
        /** @noinspection PhpUnusedParameterInspection */
        Model $Model,
        $array,
        $length
    ) {
        $array = array_values($array);
        $array = $array[0];
        foreach ($array as $v) {
            if (!Validation::maxLength($v, $length)) {
                return false;
            }
        }
        return true;
    }

    function isNotDuplicated(
        /** @noinspection PhpUnusedParameterInspection */
        Model $Model,
        $array
    ) {
        $array = array_values($array);
        $array = $array[0];
        $array = array_filter($array, "strlen");
        return count(array_unique($array)) == count($array);
    }

    function isNotExistArray(
        /** @noinspection PhpUnusedParameterInspection */
        Model $Model,
        $check,
        $compare_fields
    ) {
        $check = current($check);
        return !in_array($check, $Model->data[$Model->alias][$compare_fields]);
    }

    function birthYear(
        /** @noinspection PhpUnusedParameterInspection */
        Model $Model,
        $check
    ) {
        $value = array_values($check);
        $value = $value[0];
        return preg_match('/^\d{4}$/', $value);
    }

    function birthMonth(
        /** @noinspection PhpUnusedParameterInspection */
        Model $Model,
        $check
    ) {
        $value = array_values($check);
        $value = $value[0];
        return preg_match('/^(0[1-9]{1}|1[0-2]{1}|[1-9]{1})$/', $value);
    }

    function birthDay(
        /** @noinspection PhpUnusedParameterInspection */
        Model $Model,
        $check
    ) {
        $value = array_values($check);
        $value = $value[0];
        return preg_match('/^(0[1-9]{1}|[1-9]{1}|[1-2]{1}[0-9]{1}|3[0-1]{1})$/', $value);
    }

    function isNotEqual(
        /** @noinspection PhpUnusedParameterInspection */
        Model $Model,
        $check,
        $target
    ) {
        $value = array_values($check);
        $value = $value[0];
        $target_value = $Model->data[$Model->alias][$target];
        return $value !== $target_value;
    }

    /**
     * Checking verified email before team member activate
     *
     * @param  Model $Model [description]
     * @param  [type]  $check [description]
     *
     * @return boolean        [description]
     */
    function isVerifiedEmail(
        /** @noinspection PhpUnusedParameterInspection */
        Model $Model,
        $check
    ) {
        // This method is for to activate situation
        // So it's not needed in to inactivate situation
        if (isset($Model->data['TeamMember']['status'])
            && $Model->data['TeamMember']['status'] != TeamMember::USER_STATUS_ACTIVE
        ) {
            return true;
        }

        if (!$user_id = Hash::get($Model->data, 'TeamMember.user_id')) {
            if (!$team_member_id = Hash::get($Model->data, 'TeamMember.id')) {
                return false;
            }

            $res = $Model->find('first', [
                'conditions' => [
                    'id' => $team_member_id
                ],
                'fields'     => [
                    'id',
                    'user_id'
                ]
            ]);
            if (!$user_id = Hash::get($res, 'TeamMember.user_id')) {
                return false;
            }
        }

        $email = $Model->User->Email->find('first', [
            'conditions' => [
                'user_id' => $user_id
            ],
            'fields'     => ['id', 'email_verified']
        ]);
        if (Hash::get($email, 'Email.email_verified') == true) {
            return true;
        }
        return false;
    }

    /**
     * ユーザ名のバリデーション
     * ローマ字、スペース、アポストロフィを許可
     *
     * @param Model $Model
     * @param       $check
     *
     * @return int
     */
    function userNameChar(
        /** @noinspection PhpUnusedParameterInspection */
        Model $Model,
        $check
    ) {
        App::uses('User', 'Model');
        $value = array_values($check);
        $value = $value[0];
        return preg_match('/' . User::USER_NAME_REGEX . '/u', $value);
    }

    function numBetween(
        /** @noinspection PhpUnusedParameterInspection */
        Model $Model,
        $check,
        $lower = null,
        $upper = null
    ) {
        $check = array_values($check)[0];
        if (!is_numeric($check)) {
            return false;
        }
        if (isset($lower) && isset($upper)) {
            return ($check >= $lower && $check <= $upper);
        }
        return false;
    }

    /**
     * 全角カタカナ以外が含まれていればエラーとするバリデーションチェック
     *
     * @param Model $Model
     * @param array $value
     *
     * @return bool
     */
    function katakanaOnly(
        /** @noinspection PhpUnusedParameterInspection */
        Model $Model,
        $value
    ) {
        $value = array_shift($value);
        return preg_match("/^[ァ-ヶー゛゜]*$/u", $value);
    }
}

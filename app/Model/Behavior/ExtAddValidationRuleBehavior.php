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

     */
    function isAlphabetOnly(/** @noinspection PhpUnusedParameterInspection */
        Model $Model, $field = [])
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
    public function emailsCheck(/** @noinspection PhpUnusedParameterInspection */
        Model $Model, $value)
    {
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
}
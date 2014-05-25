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
}
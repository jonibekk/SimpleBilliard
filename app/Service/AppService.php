<?php
/**
 * Created by PhpStorm.
 * User: yoshidam2
 * Date: 2016/09/21
 * Time: 17:57
 */


/**
 * Class AppService
 */
class AppService extends Object
{
    /**
     * バリデーションメッセージの展開
     * key:valueの形にして1フィールド1メッセージにする
     *
     * @param $validationErrors
     *
     * @return array
     */
    function validationExtract($validationErrors)
    {
        $res = [];
        if (empty($validationErrors)) {
            return $res;
        }
        if ($validationErrors === true) {
            return $res;
        }
        foreach ($validationErrors as $k => $v) {
            if (is_array($v)) {
                $res[$k] = $v[0];
            } else {
                $res[$k] = $v;
            }
        }
        return $res;
    }
}

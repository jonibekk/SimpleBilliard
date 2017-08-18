<?php
App::uses('GlRedis', 'Model');
/**
 * Created by PhpStorm.
 * User: yoshidam2
 * Date: 2016/09/21
 * Time: 17:57
 */


/**
 * Class AppService
 */
class AppService extends CakeObject
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


    /**
     * Validate only specified fields and model
     *
     * @param $data
     * @param $fields
     * @param $dataParentKey
     * @param $modelKey
     * @param $model
     *
     * @return array
     */
    protected function validateSingleModelFields(
        array $data,
        array $fields,
        string $dataParentKey,
        string $modelKey,
        $model
    ): array {
        $validationFields = Hash::get($fields, $modelKey) ?? [];
        $validationBackup = $model->validate;
        // Set each field rule
        $validationRules = [];
        foreach ($validationFields as $field) {
            $validationRules[$field] = Hash::get($validationBackup, $field);
        }
        $model->validate = $validationRules;

        $checkData = Hash::get($data, $dataParentKey) ?? [];
        $model->set($checkData);
        $res = $model->validates();
        $model->validate = $validationBackup;
        if (!$res) {
            $validationErrors = $this->validationExtract($model->validationErrors);
            return [$dataParentKey => $validationErrors];
        }
        return [];

    }
}

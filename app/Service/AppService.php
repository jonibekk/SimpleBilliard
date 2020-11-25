<?php
App::uses('GlRedis', 'Model');
App::uses('TransactionManager', 'Model');

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
    /** @var TransactionManager $TransactionManager */
    protected $TransactionManager = null;

    /* variable cache not to avoid to get same data wastefully */
    protected static $cacheList = [];

    function __construct()
    {
        $this->TransactionManager = ClassRegistry::init("TransactionManager");
    }

    public function getCacheList()
    {
        return static::$cacheList;
    }

    /**
     *
     *
     * @param int $id
     * @param string $modelName
     * @param null $fields
     * @param string $key
     * @return array
     */
    protected function _getWithCache(int $id, string $modelName, $fields = null, string $key = 'id'): array
    {
        if (empty($id)) {
            return [];
        }

        $path = $modelName . "." . $id;
        // In case already got data from db and cached, but data is empty
        if (Hash::check(static::$cacheList, $path)
            && empty(Hash::get(static::$cacheList, $path))) {
            return [];
        }

        // In case already got data from db and cached, data is not empty
        $data = Hash::get(static::$cacheList, $path);
        if (!empty($data)) {
            return $data;
        }

        $model = ClassRegistry::init($modelName);

        // Get data from db and cache
        $data = $model->useType()->find('first', [
            'conditions' => [$key => $id],
            'fields' => $fields
        ]);
        $data = Hash::get($data, $modelName) ?? [];
        if (!empty($data)) {
            $data = $this->beforeCache($id, $data);
        }
        static::$cacheList[$modelName][$id] = $data;
        return $data;
    }

    /**
     * Process before saving cache if necessary
     * @param int $id
     * @param array $data
     * @return array
     */
    protected function beforeCache(int $id, array $data): array
    {
        return $data;
    }

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
     * @param array $data
     * @param array $fields
     * @param string $dataParentKey
     * @param string $modelKey
     * @param Model $model
     *
     * @return array
     */
    protected function validateSingleModelFields(
        array $data,
        array $fields,
        string $dataParentKey,
        string $modelKey,
        Model $model
    ): array
    {
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

    /**
     * @return \GuzzleHttp\Client
     */
    protected function getHttpClient(): \GuzzleHttp\Client
    {
        // use ClassRegistry::getObject() for test cases
        // usually returning false on default case
        $registeredClient = ClassRegistry::getObject(\GuzzleHttp\Client::class);
        if ($registeredClient instanceof \GuzzleHttp\Client) {
            return $registeredClient;
        }
        return new \GuzzleHttp\Client();
    }
}

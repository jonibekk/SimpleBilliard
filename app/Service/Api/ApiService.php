<?php
App::import('Service', 'AppService');

/**
 * Class ApiService
 */
class ApiService extends AppService
{
    // APIデータ取得件数上限
    const GET_MAX_LIMIT = 100;

    /**
     * APIデータ取得件数上限チェック
     * サーバーに故意に負担を掛けられるのを防ぐ為
     *
     * @param int $limit
     *
     * @return bool
     */
    function checkMaxLimit(int $limit): bool
    {
        return $limit <= self::GET_MAX_LIMIT;
    }

    /**
     * APIレスポンスデータ用に整形
     * ・Modelの頭大文字から始まるキャメルはスネークに変更(GoalApproval → goal_approval)
     * ・情報が複数件ある場合はキーを複数形にする(Comment → comments)
     *
     * @param $data
     *
     * @return array
     * @internal param $conditions
     * @internal param $offset
     * @internal param $limit
     * @internal param $order
     */
    function formatResponseData($data)
    {
        if (!is_array($data)) {
            return $data;
        }

        $ret = [];
        foreach ($data as $k => $v) {
            if (!is_array($v)) {
                $ret[$k] = $v;
                continue;
            }

            if (AppUtil::isHash($v)) {
                $newKey = Inflector::underscore($k);
                $ret[$newKey] = $this->formatResponseData($v);
            } else {
                $newKey = Inflector::underscore($k);
                $ret[Inflector::pluralize($newKey)] = $this->formatResponseData($v);
            }
        }
        return $ret;
    }

    /**
     * Generate paging array
     *
     * @param  str   $basePath
     * @param  int   $limit
     * @param  int   $offset
     * @param  array $others
     *
     * @return bool
     */
    function generatePaging(string $basePath, int $limit, int $offset, array $others = []): array
    {
        $newOffset = (int)$offset + $limit;
        $queryParams = array_merge(
            ['offset'  => $newOffset],
            compact('limit'),
            $others
        );

        $ret = [
            'next' => "${basePath}?" . http_build_query($queryParams)
        ];
        return $ret;
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

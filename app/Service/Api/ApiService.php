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
}

<?php
App::import('Service/Api', 'ApiService');

/**
 * Class ApiKeyResultService
 */
class ApiKeyResultService extends ApiService
{
    /**
     * トップページKR一覧APIレスポンスのページングデータを返す
     *
     * @return array
     */
    public function generatePagingInDashboard(int $limit, int $offset = 0, $goalId = null): array
    {
        $newOffset = (int)$offset + $limit;
        $queryParams = array_merge(
            ['offset'  => $newOffset],
            ['goal_id' => $goalId],
            compact('limit')
        );

        $ret = [
            'next' => '/api/v1/goals/dashboard_krs?' . http_build_query($queryParams)
        ];
        return $ret;
    }

    /**
     * トップページ右カラムに表示するKR一覧を取得
     * - キャッシュが必要な場合はキャッシュを扱う
     *
     * @return array
     */
    function findInDashboard(int $limit, int $offset = 0, $goalId = null, bool $needCache = false): array
    {
        /** @var KeyResult $KeyResult */
        $KeyResult = ClassRegistry::init("KeyResult");
        /** @var KeyResultService $KeyResultService */
        $KeyResultService = ClassRegistry::init("KeyResultService");
        /** @var UserService $UserService */
        $UserService = ClassRegistry::init("UserService");

        $resKrs = [];
        if ($needCache) {
            // キャッシュ検索
            $cachedKrs = Cache::read($KeyResult->getCacheKey(CACHE_KEY_KRS_IN_DASHBOARD, true), 'user_data');
            if ($cachedKrs !== false) {
                $resKrs = $cachedKrs;
            } else {
                // キャッシュが存在しない場合はquery投げて結果をキャッシュに保存
                $resKrs = $KeyResult->findInDashboard($limit, $offset);
                Cache::write($KeyResult->getCacheKey(CACHE_KEY_KRS_IN_DASHBOARD, true), $resKrs, 'user_data');
            }
        } else {
            $resKrs = $KeyResult->findInDashboard($limit, $offset, $goalId);
        }
        $resKrs = $this->formatResponseData($resKrs);
        $resKrs = $KeyResultService->processKeyResults($resKrs, 'key_result', '/');
        $resKrs = $KeyResultService->processInDashboard($resKrs);

        return $resKrs;
    }
}

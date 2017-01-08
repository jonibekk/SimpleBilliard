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
    public function generatePagingInDashboard(int $limit, ?int $offset, ?int $goalId): array
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
}

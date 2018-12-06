<?php
App::import('Lib/DataExtender', 'TeamMemberDataExtender');
App::import('Lib/DataExtender', 'UserDataExtender');
App::import('Lib/ElasticSearch', "ESClient");
App::import('Lib/ElasticSearch', "ESSearchResponse");
App::import('Service/Paging/Search', 'BaseSearchPagingService');
App::uses('User', 'Model');

/**
 * Created by PhpStorm.
 * User: Stephen Raharja
 * Date: 12/3/2018
 * Time: 10:57 AM
 */

use Goalous\Enum as Enum;

class UserSearchPagingService extends BaseSearchPagingService
{
    const ES_SEARCH_PARAM_MODEL = 'member';

    protected function setCondition(ESPagingRequest $pagingRequest): ESPagingRequest
    {
        $pagingRequest->addQueryToCondition('keyword', false);
        $pagingRequest->addQueryToCondition('limit', false, self::DEFAULT_PAGE_LIMIT);

        return $pagingRequest;
    }

    protected function fetchData(ESPagingRequest $pagingRequest): ESSearchResponse
    {
        $ESClient = new ESClient();

        $query = $pagingRequest->getCondition('keyword');

        $teamId = $pagingRequest->getTempCondition('team_id');

        $params[static::ES_SEARCH_PARAM_MODEL] = [
            'pn' => intval($pagingRequest->getCondition('pn')),
            'rn' => intval($pagingRequest->getCondition('limit'))
        ];

        return $ESClient->search($query, $teamId, $params);
    }

    protected function extendData(array $baseData, ESPagingRequest $request): array
    {
        if (empty($baseData)) {
            return [];
        }

        /** @var UserDataExtender $UserDataExtender */
        $UserDataExtender = ClassRegistry::init('UserDataExtender');
        $resultArray = $UserDataExtender->extend($baseData, "{n}.id");

        /** @var TeamMemberDataExtender $TeamMemberDataExtender */
        $TeamMemberDataExtender = ClassRegistry::init('TeamMemberDataExtender');
        $TeamMemberDataExtender->setTeamId($request->getTempCondition('team_id'));
        $resultArray = $TeamMemberDataExtender->extend($resultArray, "{n}.id", "user_id");

        foreach ($resultArray as &$result) {
            $result['display_name'] = $result['user']['display_username'] . ' (' . $result['user']['roman_username'] . ')';
            $result['img_url'] = $result['user']['profile_img_url']['medium_large'];
            $result['is_active'] = ($result['teammember']['status'] == Enum\Model\TeamMember\Status::ACTIVE);
        }

        return $resultArray;
    }

}
<?php

namespace Goalous\Service\Api;

use ESPagingRequest;
use Goalous\Enum\Api\SearchEnum;
use Goalous\Model\Api\Search\SearchApiResults;

class SearchApiService
{
    /**
     * @param string $scope
     * @param string $term
     * @param int $page
     *
     * @return SearchApiResults
     *
     * @throws \Exception
     */
    public function searchInScope($scope, $term, $page): SearchApiResults
    {
        if (false === $this->isValidScope($scope) || SearchEnum::MIN_TERM_LENGTH < strlen($term)) {
            throw new \Exception();
        }

        $results = new SearchApiResults();
        $limit = SearchEnum::SCOPE_ALL === $scope ? 3 : 20;
        $offset = SearchEnum::SCOPE_ALL === $scope ? 0 : $page * $limit;

        $pagingRequest = new ESPagingRequest();



        $pagingRequest->setQuery($query);
        $pagingRequest->addCondition('pn', 1);
        $pagingRequest->addCondition('limit', $limit);
        $pagingRequest->addCondition('type', $type);

        if (SearchEnum::SCOPE_ALL === $scope || SearchEnum::SCOPE_ACTIONS === $scope) {
            $results->addScopeResults($this->getActionResults($term, $offset, $limit));
        }

//        if (SearchApi::SCOPE_ALL === $scope || SearchApi::SCOPE_CIRCLES === $scope) {
//            $results->addScopeResults($this->getCircleResults($term, $offset, $limit));
//        }
//
//        if (SearchApi::SCOPE_ALL === $scope || SearchApi::SCOPE_GOALS === $scope) {
//            $results->addScopeResults($this->getGoalResults($term, $offset, $limit));
//        }
//
//        if (SearchApi::SCOPE_ALL === $scope || SearchApi::SCOPE_MEMBERS === $scope) {
//            $results->addScopeResults($this->getMemberResults($term, $offset, $limit));
//        }
//
//        if (SearchApi::SCOPE_ALL === $scope || SearchApi::SCOPE_POSTS === $scope) {
//            $results->addScopeResults($this->getPostResults($term, $offset, $limit));
//        }

        return $results;
    }

    /**
     * @param string $term
     * @param int $offset
     * @param int $limit
     */
    private function getActionResults($term, $offset, $limit)
    {
//        /** @var ActionSearchPagingService $ActionSearchPagingService */
//        $ActionSearchPagingService = ClassRegistry::init('ActionSearchPagingService');
//        $searchResult = $ActionSearchPagingService->getDataWithPaging($pagingRequest);
    }

    private function isValidScope($scope): bool
    {
        $allowedScopes = [
            SearchEnum::SCOPE_ACTIONS,
            SearchEnum::SCOPE_ALL,
            SearchEnum::SCOPE_CIRCLES,
            SearchEnum::SCOPE_GOALS,
            SearchEnum::SCOPE_MEMBERS,
            SearchEnum::SCOPE_POSTS
        ];

        return in_array($scope, $allowedScopes);
    }

    private function isValidRequest(): array
    {
        $userId = $this->Auth->user('id');
        $teamId = $this->current_team_id;

        if (empty ($userId)) {
            return ["No user ID"];
        }

        if (empty($teamId)) {
            return ["No team ID"];
        }

        return [];
    }
}

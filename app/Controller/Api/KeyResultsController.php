
<?php
App::uses('BasePagingController', 'Controller/Api');
App::uses('KeyResult', 'Model');
App::import('Service', 'GroupService');
App::import('Controller/Traits/Notification', 'TranslationNotificationTrait');
App::import('Service', 'ImageStorageService');
App::import('Service', 'WatchlistService');

use Goalous\Exception as GlException;

class KeyResultsController extends BasePagingController
{
    use TranslationNotificationTrait;

    public $components = [
        'NotifyBiz',
    ];

    public function put_toggle_watch($id)
    {
        $requestData = $this->getRequestJsonBody();
        $userId = $this->getUserId();
        $teamId = $this->getTeamId();
        $ret = [];

        try {
            $kr = $this->findKr($id);
            $watched = $requestData['watched'];

            /** @var WatchlistService */
            $WatchlistService = ClassRegistry::init("WatchlistService");

            if ($watched) {
                $watchlist = $WatchlistService->add($userId, $teamId, $kr['id']);
            } else {
                $watchlist = $WatchlistService->remove($userId, $teamId, $kr['id']);
            }
        } catch(Exception $e) {
            return $this->generateResponseIfException($e);
        }

        $ret['is_watched'] = $watched;
        $ret['watchlist_id'] = $watchlist['Watchlist']['id'];
        return ApiResponse::ok()->withData($ret)->getResponse();
    }

    private function findKr(int $krId)
    {
        $this->loadModel('KeyResult');
        $kr = $this->KeyResult->useType()->getById($krId);

        if (empty($kr)) {
            throw new GlException\GoalousNotFoundException(__("This key result doesn't exist."));
        }

        return $kr;
    }
}

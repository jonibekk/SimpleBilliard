<?php
App::uses('ApiController', 'Controller/Api');
App::uses('PostDraft', 'Model');
App::import('Service', 'GoalService');

/**
 * Class PostDraftController
 *
 * @property NotificationComponent Notification
 */
class PostDraftsController extends ApiController
{
    public $components = [
        'Notification',
    ];

    public function post_delete()
    {
        $postDraftId = $this->request->params['id'];

        /** @var PostDraft $PostDraft */
        $PostDraft = ClassRegistry::init("PostDraft");
        $postDraft = $PostDraft->getById($postDraftId);
        if (empty($postDraft)) {
            $this->Notification->outError(__('Draft is deleted'));
            return $this->redirect($this->referer());
        }

        $userId = $this->Auth->user('id');

        if (intval($postDraft['user_id']) !== intval($userId)) {
            CakeLog::notice(sprintf('delete draft post canceled, user id is not match %s', AppUtil::jsonOneLine([
                'post_drafts.user_id' => $postDraft['user_id'],
                'user_id'             => $userId,
            ])));
            return $this->redirect($this->referer());
        }
        if (intval($postDraft['team_id']) !== intval($this->current_team_id)) {
            CakeLog::notice(sprintf('delete draft post canceled, team id is not match %s', AppUtil::jsonOneLine([
                'post_drafts.team_id' => $postDraft['team_id'],
                'team_id'             => $this->current_team_id,
            ])));
            $this->Notification->outError(__('current team is not match'));
            return $this->redirect($this->referer());
        }

        CakeLog::info(sprintf('deleted draft post %s', AppUtil::jsonOneLine([
            'post_drafts.id' => $postDraft['id'],
        ])));
        $this->Notification->outSuccess(__('Deleted the draft.'));
        $PostDraft->delete($postDraft);
        return $this->redirect($this->referer());
    }
}

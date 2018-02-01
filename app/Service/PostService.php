<?php
App::import('Service', 'AppService');
App::uses('Circle', 'Model');
App::uses('PostShareUser', 'Model');
App::uses('User', 'Model');
App::uses('PostShareCircle', 'Model');
App::uses('PostFile', 'Model');
App::uses('PostResource', 'Model');
App::uses('Circle', 'Model');
App::uses('Post', 'Model');
App::uses('AttachedFile', 'Model');

use Goalous\Model\Enum as Enum;

/**
 * Class PostService
 */
class PostService extends AppService
{

    /**
     * 月のインデックスからフィードの取得期間を取得
     *
     * @param int $monthIndex
     *
     * @return array ['start'=>unixtimestamp,'end'=>unixtimestamp]
     */
    function getRangeByMonthIndex(int $monthIndex): array
    {
        $start_month_offset = $monthIndex + 1;
        $ret['end'] = strtotime("-{$monthIndex} months", REQUEST_TIMESTAMP);
        $ret['start'] = strtotime("-{$start_month_offset} months", REQUEST_TIMESTAMP);
        return $ret;
    }


    /**
     * TODO:
     */
    public function addNormalFromPostDraft(array $postDraft): array
    {
        try {
            $this->TransactionManager->begin();
            $post = $this->addNormal(
                json_decode($postDraft['draft_data'], true),
                $postDraft['user_id'],
                $postDraft['team_id'],
                // If draft is created, post resources is also created
                []
            );

            /** @var PostResourceService $PostResourceService */
            $PostResourceService = ClassRegistry::init('PostResourceService');
            // changing post_resources.post_id = null to posts.id
            $PostResourceService->updatePostIdByPostDraftId($post['id'], $postDraft['id']);

            /** @var PostDraft $PostDraft */
            $PostDraft = ClassRegistry::init('PostDraft');
            $postDraft['post_id'] = $post['id'];
            $PostDraft->save($postDraft);

            // Post is created by PostDraft
            // Deleting PostDraft because target PostDraft ended role
            $PostDraft->softDelete($postDraft['id'], false);
            $this->TransactionManager->commit();
            return $post;
        } catch (Exception $e) {
            $this->TransactionManager->rollback();
            GoalousLog::error('failed adding post data', [
                'message' => $e->getMessage(),
                'users.id' => $userId,
                'teams.id' => $teamId,
            ]);
            GoalousLog::error($e->getTraceAsString());
        }
        return false;
    }



    /**
     * Adding new normal post with transaction
     *
     * @param array $postData
     * @param int   $userId
     * @param int   $teamId
     * @param array $postResources
     *
     * @return array|bool If success, returns posts data array, if failed, returning false
     */
    function addNormalWithTransaction(array $postData, int $userId, int $teamId, array $postResources = [])
    {
        try {
            $this->TransactionManager->begin();
            $post = $this->addNormal(
                $postData, $userId, $teamId, $postResources
            );
            $this->TransactionManager->commit();
            return $post;
        } catch (Exception $e) {
            $this->TransactionManager->rollback();
            GoalousLog::error('failed adding post data', [
                'message' => $e->getMessage(),
                'users.id' => $userId,
                'teams.id' => $teamId,
            ]);
            GoalousLog::error($e->getTraceAsString());
        }
        return false;
    }

    /**
     * Adding new normal post
     * Be careful, no transaction in this method
     * You should write try-catch and transaction yourself outside of this function
     *
     * @param array $postData
     * @param int   $userId
     * @param int   $teamId
     * @param array $postResources array data of post_resources
     *
     * @return array Always return inserted post data array if succeed
     *      otherwise throwing exception
     *
     * @throws \InvalidArgumentException
     *      If passed data is invalid or not enough, throws InvalidArgumentException
     * @throws \RuntimeException
     *      If failing on adding post, this function always throws exception in any case
     * @throws (\Throwable) will not throw this, but should define here
     *      PhpStorm shows warn because \Throwable appear in codes
     */
    function addNormal(array $postData, int $userId, int $teamId, array $postResources = []): array
    {
        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');
        /** @var PostShareUser $PostShareUser */
        $PostShareUser = ClassRegistry::init('PostShareUser');
        /** @var User $User */
        $User = ClassRegistry::init('User');
        /** @var PostShareCircle $PostShareCircle */
        $PostShareCircle = ClassRegistry::init('PostShareCircle');
        /** @var PostResource $PostResource */
        $PostResource = ClassRegistry::init('PostResource');
        /** @var PostFile $PostFile */
        $PostFile = ClassRegistry::init('PostFile');
        /** @var Circle $Circle */
        $Circle = ClassRegistry::init('Circle');

        // TODO: should be fix for better system
        // Having deep dependence on each class's property(my_uid, current_team_id).
        // These property is null. If method used on the session called by "batch shell" or "non-auth routing".
        $PostFile->AttachedFile->current_team_id = $teamId;
        $PostFile->AttachedFile->my_uid = $userId;
        $PostFile->AttachedFile->PostFile->current_team_id = $teamId;
        $PostFile->AttachedFile->PostFile->my_uid = $userId;

        // Why? if using this line, the Post record increase on CakePHP fixture
        //$PostFile->AttachedFile->PostFile->Post->current_team_id = $teamId;
        //$PostFile->AttachedFile->PostFile->Post->my_uid = $userId;
        $PostFile->AttachedFile->PostFile->Post->PostShareCircle->current_team_id = $teamId;
        $PostFile->AttachedFile->PostFile->Post->PostShareCircle->my_uid = $userId;
        $PostFile->AttachedFile->PostFile->Post->PostShareUser->current_team_id = $teamId;
        $PostFile->AttachedFile->PostFile->Post->PostShareUser->my_uid = $userId;
        $User->CircleMember->current_team_id = $teamId;
        $User->CircleMember->my_uid = $userId;
        $PostShareCircle->current_team_id = $teamId;
        $PostShareCircle->my_uid = $userId;
        $PostShareCircle->Circle->current_team_id = $teamId;
        $PostShareCircle->Circle->my_uid = $userId;

        if (!isset($postData['Post']) || empty($postData['Post'])) {
            GoalousLog::error('Error on adding post: Invalid argument', [
                'users.id' => $userId,
                'teams.id' => $teamId,
                'postData' => $postData,
            ]);
            throw new InvalidArgumentException('Error on adding post: Invalid argument');
        }
        $share = null;
        if (isset($postData['Post']['share']) && !empty($postData['Post']['share'])) {
            $share = explode(",", $postData['Post']['share']);
            foreach ($share as $key => $val) {
                if (stristr($val, 'public')) {
                    $teamAllCircle = $Circle->getTeamAllCircle($teamId);
                    $share[$key] = 'circle_' . $teamAllCircle['Circle']['id'];
                }
            }
        }
        $postData['Post']['user_id'] = $userId;
        $postData['Post']['team_id'] = $teamId;
        if (!isset($postData['Post']['type'])) {
            $postData['Post']['type'] = Post::TYPE_NORMAL;
        }

        $Post->create();
        $post = $Post->save($postData, [
            'atomic' => false,
        ]);
        if (empty($post)) {
            GoalousLog::error('Error on adding post: failed post save', [
                'users.id' => $userId,
                'teams.id' => $teamId,
                'postData' => $postData,
            ]);
            throw new RuntimeException('Error on adding post: failed post save');
        }

        $postId = $post['Post']['id'];
        // If posted with attach files
        if (isset($postData['file_id']) && is_array($postData['file_id'])) {
            if (false === $PostFile->AttachedFile->saveRelatedFiles($postId,
                AttachedFile::TYPE_MODEL_POST,
                $postData['file_id'])) {
                throw new RuntimeException('Error on adding post: failed saving related files');
            }
        }
        // Handling post resources
        foreach ($postResources as $postResource) {
            $PostResource->create();
            $postResource = $PostResource->save([
                'post_id'       => $postId,
                'post_draft_id' => null,
                // TODO: currently only resource type of video only
                // need to determine what type of resource is passed from arguments
                // (maybe should wrap by class, not simple array)
                // same as in PostDraftService::createPostDraftWithResources()
                'resource_type' => Enum\Post\PostResourceType::VIDEO_STREAM()->getValue(),
                'resource_id'   => $postResource['id'],
            ], [
                'atomic' => false
            ]);
            $postResource = reset($postResource);
        }

        if (!empty($share)) {
            // ユーザとサークルに分割
            $users = [];
            $circles = [];
            foreach ($share as $val) {
                // ユーザの場合
                if (stristr($val, 'user_')) {
                    $users[] = str_replace('user_', '', $val);
                } // サークルの場合
                elseif (stristr($val, 'circle_')) {
                    $circles[] = str_replace('circle_', '', $val);
                }
            }
            if ($users) {
                // Save share users
                if (false === $PostShareUser->add($postId, $users)) {
                    throw new RuntimeException('PostShareUser->add share user');// TODO:
                }
            }
            if ($circles) {
                try {
                    // Save share circles
                    if (false === $PostShareCircle->add($postId, $circles, $teamId)) {
                        throw new RuntimeException('PostShareCircle->add');// TODO:
                    }
                    // Update unread post numbers if specified sharing circle
                    if (false === $User->CircleMember->incrementUnreadCount($circles, true, $teamId)) {
                        throw new RuntimeException('CircleMember->incrementUnreadCount');// TODO:
                    }
                    // Update modified date if specified sharing circle
                    if (false === $User->CircleMember->updateModified($circles, $teamId)) {
                        throw new RuntimeException('CircleMember->updateModified');// TODO:
                    }
                    // Same as above
                    if (false === $PostShareCircle->Circle->updateModified($circles)) {
                        throw new RuntimeException('PostShareCircle->Circle->updateModified');// TODO:
                    }
                } catch (\Throwable $e) {
                    $PostFile->AttachedFile->deleteAllRelatedFiles($postId, AttachedFile::TYPE_MODEL_POST);
                    throw $e;
                }
            }
        }

        // If attached file is specified, deleting temporary updated files
        if (isset($postData['file_id']) && is_array($postData['file_id'])) {
            /** @var GlRedis $GlRedis */
            $GlRedis = ClassRegistry::init('GlRedis');
            foreach ($postData['file_id'] as $hash) {
                $GlRedis->delPreUploadedFile($teamId, $userId, $hash);
            }
        }

        return reset($post);
    }



    /**
     * ユーザIDとチームIDをセット
     *
     * @param null $uid
     * @param null $team_id
     */
    public function setUidAndTeamId($uid = null, $team_id = null)
    {
        $this->setUid($uid);
        $this->setTeamId($team_id);
    }


    /**
     * ユーザIDをセット
     *
     * @param null $uid
     */
    public function setUid($uid = null)
    {
        if (!$uid) {
            $this->uid = $this->my_uid;
        } else {
            $this->uid = $uid;
        }
    }

    /**
     * チームIDをセット
     *
     * @param null $team_id
     */
    public function setTeamId($team_id = null)
    {
        if (!$team_id) {
            $this->team_id = $this->current_team_id;
        } else {
            $this->team_id = $team_id;
        }
    }
}

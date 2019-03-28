<?php
App::import('Service', 'AppService');
App::uses('AttachedFile', 'Model');
App::import('Service', 'PostFileService');
App::import('Service', 'AttachedFileService');
App::import('Service', 'UploadService');
App::import('Service', 'VideoStreamService');
App::import('Lib/Storage', 'UploadedFile');
App::import('Service', 'PostResourceService');
App::uses('Circle', 'Model');
App::uses('PostShareUser', 'Model');
App::uses('PostShareCircle', 'Model');
App::uses('PostDraft', 'Model');
App::uses('PostRead', 'Model');
App::uses('PostMention', 'Model');
App::uses('PostLike', 'Model');
App::uses('PostFile', 'Model');
App::uses('PostResource', 'Model');
App::uses('PostSharedLog', 'Model');
App::uses('CircleMember', 'Model');
App::uses('Post', 'Model');
App::uses('User', 'Model');
App::import('Model/Entity', 'PostEntity');
App::import('Model/Entity', 'PostFileEntity');
App::import('Model/Entity', 'CircleEntity');
App::import('Model/Entity', 'AttachedFileEntity');
App::import('Model/Entity', 'PostFileEntity');
App::import('Model/Entity', 'PostResourceEntity');
App::import('Lib/DataExtender', 'PostExtender');
App::import('Lib/Cache/Redis/UnreadPosts', 'UnreadPostsClient');
App::import('Lib/Cache/Redis/UnreadPosts', 'UnreadPostsKey');
App::import('Service/Redis', 'UnreadPostsRedisService');

use Goalous\Enum as Enum;
use Goalous\Enum\Model\AttachedFile\AttachedFileType as AttachedFileType;
use Goalous\Enum\Model\AttachedFile\AttachedModelType as AttachedModelType;
use Goalous\Exception as GlException;

/**
 * Class PostService
 */
class PostService extends AppService
{
    /**
     * Get single data based on model.
     * extend data
     *
     * @param PostResourceRequest $req
     * @param array               $extensions
     *
     * @return array
     */
    public function get(PostResourceRequest $req, array $extensions = []): array
    {
        $id = $req->getId();
        $userId = $req->getUserId();
        $teamId = $req->getTeamId();
        if ($req->isCheckPermission() && !$this->canAccessPost($id, $userId, $teamId)
        ) {
            return [];
        }

        $data = $this->_getWithCache($id, 'Post');
        if (empty($data)) {
            return [];
        }

        /** @var PostExtender $PostExtender */
        $PostExtender = ClassRegistry::init('PostExtender');

        $data = $PostExtender->extend($data, $userId, $teamId, $extensions);
        return $data;
    }

    function canAccessPost(int $postId, int $userId, int $teamId)
    {
        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');
        $condition = ['id' => $postId, 'team_id' => $teamId];
        $data = $Post->useEntity()->useType()->find('first', $condition);
        if (empty($data)) {
            return false;
        }
        // Check whether self post
        if ((int)$data['user_id'] === $userId) {
            return true;
        }

        // Check if goal post
        if ($Post->isGoalPost($postId, $userId, $teamId)) {
            return true;
        }

        // Check circle post permission
        try {
            if ($this->checkUserAccessToCirclePost($userId, $postId)) {
                return true;
            }
        } catch (Exception $exception) {
            return false;
        }
        return false;
    }

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
     * Adding new normal post from post_draft data
     *
     * @param array $postDraft
     *
     * @return array|false
     */
    public function addNormalFromPostDraft(array $postDraft)
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
            if (false === $PostResourceService->updatePostIdByPostDraftId($post['id'], $postDraft['id'])) {
                GoalousLog::error($errorMessage = 'failed updating post_resources.post_id', [
                    'posts.id'       => $post['id'],
                    'post_drafts.id' => $postDraft['id'],
                ]);
                throw new RuntimeException('Error on adding post from draft: ' . $errorMessage);
            }

            /** @var PostDraft $PostDraft */
            $PostDraft = ClassRegistry::init('PostDraft');
            $postDraft['post_id'] = $post['id'];
            if (false === $PostDraft->save($postDraft)) {
                GoalousLog::error($errorMessage = 'failed saving post_draft', [
                    'posts.id'       => $post['id'],
                    'post_drafts.id' => $postDraft['id'],
                ]);
                throw new RuntimeException('Error on adding post from draft: ' . $errorMessage);
            }

            // Post is created by PostDraft
            // Deleting PostDraft because target PostDraft ended role
            // Could not judge if delete() is succeed or not (always returning false)
            $PostDraft->delete($postDraft['id']);
            $this->TransactionManager->commit();
            return $post;
        } catch (Exception $e) {
            $this->TransactionManager->rollback();
            GoalousLog::error('failed adding post data from draft', [
                'message' => $e->getMessage(),
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
        } catch (\Throwable $e) {
            $this->TransactionManager->rollback();

            // Logging for https://jira.goalous.com/browse/GL-7496
            if (false !== strpos($e->getMessage(), 'Lock wait timeout exceeded')) {
                GoalousLog::emergency('Got a DB lock when adding post', [
                    'message'  => $e->getMessage(),
                    'users.id' => $userId,
                    'teams.id' => $teamId,
                ]);
                GoalousLog::emergency($e->getTraceAsString());
            } else {
                GoalousLog::error('failed adding post data', [
                    'message'  => $e->getMessage(),
                    'users.id' => $userId,
                    'teams.id' => $teamId,
                ]);
                GoalousLog::error($e->getTraceAsString());
            }
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
        /** @var PostResourceService $PostResourceService */
        $PostResourceService = ClassRegistry::init('PostResourceService');

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

        $hasVideoStream = false;
        // Handling post resources
        // This is the legacy code, only handling video stream on here.
        // See the image or document file for $postData['file_id'] valuable
        foreach ($postResources as $postResource) {
            $hasVideoStream = true;
            $PostResourceService->addResourcePost($postId,
                Enum\Model\Post\PostResourceType::VIDEO_STREAM(),
                $postResource['id'],
                $order = 0);
        }

        // If posted with attach files
        if (isset($postData['file_id']) && is_array($postData['file_id'])) {
            if (false === $PostFile->AttachedFile->saveRelatedFiles($postId,
                    AttachedFile::TYPE_MODEL_POST,
                    $postData['file_id'],
                    $hasVideoStream)
            ) {
                throw new RuntimeException('Error on adding post: failed saving related files');
            }
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
                    GoalousLog::error($errorMessage = 'failed saving post share users', [
                        'posts.id'  => $postId,
                        'users.ids' => $users,
                    ]);
                    throw new RuntimeException('Error on adding post: ' . $errorMessage);
                }
            }
            if ($circles) {
                try {
                    // Save share circles
                    if (false === $PostShareCircle->add($postId, $circles, $teamId)) {
                        GoalousLog::error($errorMessage = 'failed saving post share circles', [
                            'posts.id'    => $postId,
                            'circles.ids' => $postId,
                            'teams.id'    => $teamId,
                        ]);
                        throw new RuntimeException('Error on adding post: ' . $errorMessage);
                    }
                    // Update unread post numbers if specified sharing circle
                    if (false === $User->CircleMember->incrementUnreadCount($circles, true, $teamId)) {
                        GoalousLog::error($errorMessage = 'failed increment unread count', [
                            'circles.ids' => $postId,
                            'teams.id'    => $teamId,
                        ]);
                        throw new RuntimeException('Error on adding post: ' . $errorMessage);
                    }
                    // Update modified date if specified sharing circle
                    if (false === $User->CircleMember->updateModified($circles, $teamId)) {
                        GoalousLog::error($errorMessage = 'failed update modified of circle member', [
                            'circles.ids' => $circles,
                            'teams.id'    => $teamId,
                        ]);
                        throw new RuntimeException('Error on adding post: ' . $errorMessage);
                    }
                    // Same as above
                    if (false === $PostShareCircle->Circle->updateModified($circles)) {
                        GoalousLog::error($errorMessage = 'failed update modified of circles', [
                            'circles.ids' => $circles,
                        ]);
                        throw new RuntimeException('Error on adding post: ' . $errorMessage);
                    }
                    // Update last_post_created in circle
                    if (false === $Circle->updateLatestPostedInCircles($circles)) {
                        GoalousLog::error($errorMessage = 'failed updating last post created', [
                            'post.id'    => $postId,
                            'circles.id' => $circles,
                            'teams.id'   => $teamId,
                        ]);
                        throw new RuntimeException('Error on adding post: ' . $errorMessage);
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
     * Save favorite post
     *
     * @param int $postId
     * @param int $userId
     * @param int $teamId
     *
     * @return bool
     */
    function saveItem(int $postId, int $userId, int $teamId): bool
    {
        /** @var SavedPost $SavedPost */
        $SavedPost = ClassRegistry::init("SavedPost");

        try {
            $SavedPost->create();
            $SavedPost->save([
                'post_id' => $postId,
                'user_id' => $userId,
                'team_id' => $teamId,
            ]);
        } catch (Exception $e) {
            CakeLog::error(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            CakeLog::error($e->getTraceAsString());
            return false;
        }
        return true;
    }

    /**
     * Delete favorite post
     *
     * @param int $postId
     * @param int $userId
     *
     * @return bool
     */
    function deleteItem(int $postId, int $userId): bool
    {
        /** @var SavedPost $SavedPost */
        $SavedPost = ClassRegistry::init("SavedPost");

        try {
            $SavedPost->deleteAll([
                'post_id' => $postId,
                'user_id' => $userId,
            ]);
        } catch (Exception $e) {
            CakeLog::error(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            CakeLog::error($e->getTraceAsString());
            return false;
        }
        return true;
    }

    /**
     * Method to save a circle post
     *
     * @param array   $postBody
     *                   ["body" => '',
     *                   "type" => ''
     *                   ]
     * @param int     $circleId
     * @param int     $userId
     * @param int     $teamId
     * @param array[] $files
     *                   [
     *                   {"file_uuid": "5c3eae43d92d06.36873270"},
     *                   {"is_video": true, "video_stream_id": "33"},
     *                   ...
     *                   ]
     *
     * @return PostEntity Entity of saved post
     * @throws Exception
     */
    public function addCirclePost(
        array $postBody,
        int $circleId,
        int $userId,
        int $teamId,
        array $files = []
    ): PostEntity
    {
        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');
        /** @var PostShareCircle $PostShareCircle */
        $PostShareCircle = ClassRegistry::init('PostShareCircle');
        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init('CircleMember');
        /** @var Circle $Circle */
        $Circle = ClassRegistry::init('Circle');

        if (empty($postBody['body'])) {
            GoalousLog::error('Error on adding post: Invalid argument', [
                'users.id'  => $userId,
                'circle.id' => $circleId,
                'teams.id'  => $teamId,
                'postData'  => $postBody,
            ]);
            throw new InvalidArgumentException('Error on adding post: Invalid argument');
        }

        try {
            $this->TransactionManager->begin();
            $Post->create();

            $postBody['user_id'] = $userId;
            $postBody['team_id'] = $teamId;

            if ($postBody['type'] == Post::TYPE_CREATE_CIRCLE) {
                $postBody['circle_id'] = $circleId;
            } elseif (empty($postBody['type'])) {
                $postBody['type'] = Post::TYPE_NORMAL;
            }

            // OGP
            $postBody['site_info'] = !empty($postBody['site_info']) ? json_encode($postBody['site_info']) : null;

            /** @var PostEntity $savedPost */
            $savedPost = $Post->useType()->useEntity()->save($postBody, false);

            if (empty ($savedPost)) {
                GoalousLog::error('Error on adding post: failed post save', [
                    'users.id'  => $userId,
                    'circle.id' => $circleId,
                    'teams.id'  => $teamId,
                    'postData'  => $postBody,
                ]);
                throw new RuntimeException('Error on adding post: failed post save');
            }

            $postId = $savedPost['id'];
            $postCreated = $savedPost['created'];

            //Update last_posted time
            $updateCondition = [
                'CircleMember.user_id'   => $userId,
                'CircleMember.circle_id' => $circleId
            ];

            if (!$CircleMember->updateAll(['last_posted' => $postCreated], $updateCondition)) {
                GoalousLog::error($errorMessage = 'failed updating last_posted in circle_members', [
                    'posts.id'    => $postId,
                    'circles.ids' => $circleId,
                    'teams.id'    => $teamId,
                    'users.id'    => $userId,
                ]);
                throw new RuntimeException('Error on adding post: ' . $errorMessage);
            }

            // Save share circles
            if (false === $PostShareCircle->add($postId, [$circleId], $teamId)) {
                GoalousLog::error($errorMessage = 'failed saving post share circles', [
                    'posts.id'    => $postId,
                    'circles.ids' => $circleId,
                    'teams.id'    => $teamId,
                ]);
                throw new RuntimeException('Error on adding post: ' . $errorMessage);
            }
            // Update unread post numbers if specified sharing circle
            if (false === $CircleMember->incrementUnreadCount([$circleId], true, $teamId, $userId)) {
                GoalousLog::error($errorMessage = 'failed increment unread count', [
                    'post.id'    => $postId,
                    'circles.id' => $circleId,
                    'teams.id'   => $teamId,
                ]);
                throw new RuntimeException('Error on adding post: ' . $errorMessage);
            }
            // Update last_post_created in circle
            if (false === $Circle->updateLatestPosted($circleId)) {
                GoalousLog::error($errorMessage = 'failed updating last post created', [
                    'post.id'    => $postId,
                    'circles.id' => $circleId,
                    'teams.id'   => $teamId,
                ]);
                throw new RuntimeException('Error on adding post: ' . $errorMessage);
            }

            //Save attached files
            if (!empty($files)) {
                $this->saveFiles($postId, $userId, $teamId, $files);
            }

            $this->TransactionManager->commit();

        } catch (Exception $e) {
            $this->TransactionManager->rollback();
            throw $e;
        }

        /** @var UnreadPostsRedisService $UnreadPostsRedisService */
        $UnreadPostsRedisService = ClassRegistry::init('UnreadPostsRedisService');
        $UnreadPostsRedisService->addToAllCircleMembers($circleId, $postId, $userId);

        return $savedPost;
    }

    /**
     * Check whether the user can view the post
     *
     * @param int  $userId
     * @param int  $postId
     * @param bool $mustBelong Whether user must belong to the circle where post is shared to
     *
     * @return bool
     */
    public function checkUserAccessToCirclePost(int $userId, int $postId, bool $mustBelong = false): bool
    {
        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init("CircleMember");

        /** @var Circle $Circle */
        $Circle = ClassRegistry::init('Circle');
        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');

        $post = $Post->findById($postId);
        if (empty($post)) {
            throw new GlException\GoalousNotFoundException(__("This post doesn't exist."));
        }

        $circleOption = [
            'conditions' => [
                'PostShareCircle.post_id' => $postId,
            ],
            'fields'     => [
                'Circle.id',
                'Circle.public_flg',
                'Circle.team_all_flg'
            ],
            'table'      => 'circles',
            'alias'      => 'Circle',
            'joins'      => [
                [
                    'type'       => 'INNER',
                    'conditions' => [
                        'Circle.id = PostShareCircle.circle_id',
                    ],
                    'table'      => 'post_share_circles',
                    'alias'      => 'PostShareCircle',
                    'field'      => 'PostShareCircle.circle_id'
                ]
            ]
        ];

        /** @var CircleEntity[] $circles */
        $circles = $Circle->useType()->useEntity()->find('all', $circleOption);

        if (empty($circles)) {
            throw new GlException\GoalousNotFoundException(__("This post doesn't exist."));
        }

        $circleArray = [];

        foreach ($circles as $circle) {
            $circleArray[] = $circle['id'];
            //If circle is public or team_all, return true
            if (!$mustBelong && ($circle['public_flg'] || $circle['team_all_flg'])) {
                return true;
            }
        }

        $circleMemberOption = [
            'conditions' => [
                'CircleMember.circle_id' => $circleArray,
                'CircleMember.user_id'   => $userId,
                'CircleMember.del_flg'   => false
            ],
            'table'      => 'circle_members',
            'alias'      => 'CircleMember',
            'fields'     => 'CircleMember.circle_id'
        ];

        $circleList = (int)$CircleMember->find('count', $circleMemberOption) ?? 0;

        return $circleList > 0;
    }

    /**
     * Get list of attached files of a post despite of post typte
     *
     * @param int                                              $postId
     * @param Goalous\Enum\Model\AttachedFile\AttachedFileType $type Filtered file type
     *
     * @return AttachedFileEntity[]
     */
    public function getAttachedFiles(int $postId, AttachedFileType $type = null): array
    {
        $files = $this->getNormalAttachedFiles($postId, $type);
        if (!empty($files)) {
            return $files;
        }
        $files = $this->getActionAttachedFiles($postId, $type);
        return $files;
    }

    /**
     * Get list of normal attached files(e.g. circle post) of a post
     *
     * @param int                                              $postId
     * @param Goalous\Enum\Model\AttachedFile\AttachedFileType $type Filtered file type
     *
     * @return AttachedFileEntity[]
     */
    public function getNormalAttachedFiles(int $postId, AttachedFileType $type = null): array
    {
        /** @var AttachedFile $AttachedFile */
        $AttachedFile = ClassRegistry::init('AttachedFile');

        $conditions = [
            'conditions' => [],
            'table'      => 'attached_files',
            'alias'      => 'AttachedFile',
            'joins'      => [
                [
                    'type'       => 'INNER',
                    'table'      => 'post_files',
                    'alias'      => 'PostFile',
                    'conditions' => [
                        'PostFile.post_id' => $postId,
                        'PostFile.attached_file_id = AttachedFile.id'
                    ]
                ],
            ]
        ];

        if (!empty($type)) {
            $conditions['conditions']['file_type'] = $type->getValue();
        }

        return $AttachedFile->useType()->useEntity()->find('all', $conditions);
    }

    public function getResourcesByPostId(int $postId): array
    {
        /** @var PostResource $PostResource */
        $PostResource = ClassRegistry::init('PostResource');

        $conditions = [
            'fields'     => [
                'PostResource.*',
                'AttachedFile.*',
            ],
            'table'      => 'post_resources',
            'alias'      => 'PostResource',
            'conditions' => [
                'PostResource.post_id' => $postId,
            ],
            'joins'      => [
                [
                    'type'       => 'LEFT',
                    'table'      => 'attached_files',
                    'alias'      => 'AttachedFile',
                    'conditions' => [
                        'AttachedFile.id = PostResource.resource_id',
                        'PostResource.resource_type' => [
                            Enum\Model\Post\PostResourceType::IMAGE,
                            Enum\Model\Post\PostResourceType::FILE,
                            Enum\Model\Post\PostResourceType::FILE_VIDEO,
                        ],
                    ]
                ],
            ],
            'order'      => ['PostResource.resource_order' => 'ASC'],
        ];

        return $PostResource->useType()->useEntity()->find('all', $conditions);
    }

    /**
     * Get list of action attached files of a post
     *
     * @param int                                              $postId
     * @param Goalous\Enum\Model\AttachedFile\AttachedFileType $type Filtered file type
     *
     * @return AttachedFileEntity[]
     */
    public function getActionAttachedFiles(int $postId, AttachedFileType $type = null): array
    {
        /** @var AttachedFile $AttachedFile */
        $AttachedFile = ClassRegistry::init('AttachedFile');

        $conditions = [
            'conditions' => [],
            'table'      => 'attached_files',
            'alias'      => 'AttachedFile',
            'joins'      => [
                [
                    'type'       => 'INNER',
                    'table'      => 'action_result_files',
                    'alias'      => 'ActionResultFile',
                    'conditions' => [
                        'ActionResultFile.attached_file_id = AttachedFile.id'
                    ]
                ],
                [
                    'type'       => 'INNER',
                    'table'      => 'posts',
                    'alias'      => 'Post',
                    'conditions' => [
                        'Post.id' => $postId,
                        'Post.action_result_id = ActionResultFile.action_result_id'
                    ]
                ]
            ]
        ];

        if (!empty($type)) {
            $conditions['conditions']['file_type'] = $type->getValue();
        }

        return $AttachedFile->useType()->useEntity()->find('all', $conditions);
    }

    /**
     * Soft delete circle post and its related data
     *
     * @param int $postId
     *
     * @return bool
     * @throws Exception
     */
    public function softDelete(int $postId): bool
    {
        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');
        /** @var PostResource $PostResource */
        $PostResource = ClassRegistry::init('PostResource');

        //Check if post exists & not deleted
        $postCondition = [
            'conditions' => [
                'id'      => $postId,
                'del_flg' => false
            ]
        ];
        if (empty($Post->find('first', $postCondition))) {
            throw new GlException\GoalousNotFoundException(__("This post doesn't exist."));
        }

        $modelsToDelete = [
            'PostDraft'       => 'post_id',
            'PostLike'        => 'post_id',
            'PostMention'     => 'post_id',
            'PostRead'        => 'post_id',
            'PostShareCircle' => 'post_id',
            'PostShareUser'   => 'post_id',
            'Post'            => 'Post.id'
        ];

        try {
            $this->TransactionManager->begin();

            foreach ($modelsToDelete as $model => $column) {
                /** @var AppModel $Model */
                $Model = ClassRegistry::init($model);

                $condition = [$column => $postId];

                $res = $Model->softDeleteAll($condition, false);
                if (!$res) {
                    throw new RuntimeException("Error on deleting ${model} for post $postId: failed post soft delete");
                }
            }

            //Delete post resources
            $deletedPosts = $PostResource->getAllPostResources($postId);

            if (!empty($deletedPosts)) {
                /** @var PostResourceService $PostResourceService */
                $PostResourceService = ClassRegistry::init('PostResourceService');
                $PostResourceService->deleteResources(Hash::extract($deletedPosts, '{n}.id'));
            }

            $this->TransactionManager->commit();
        } catch (Exception $e) {
            $this->TransactionManager->rollback();
            GoalousLog::error("Error on deleting post $postId: failed post soft delete", $e->getTrace());
            throw $e;
        }

        return true;
    }

    /**
     * Save all attached files
     *
     * @param int   $postId
     * @param int   $userId
     * @param int   $teamId
     * @param array $files         Refer addCirclePost() method document
     * @param bool  $isDraft
     * @param int   $postFileIndex Custom starting index for post files
     *
     * @return bool
     * @throws Exception
     */
    public function saveFiles(int $postId, int $userId, int $teamId, array $files, bool $isDraft = false, int $postFileIndex = 0): bool
    {
        /** @var UploadService $UploadService */
        $UploadService = ClassRegistry::init('UploadService');
        /** @var AttachedFileService $AttachedFileService */
        $AttachedFileService = ClassRegistry::init('AttachedFileService');
        /** @var PostFileService $PostFileService */
        $PostFileService = ClassRegistry::init('PostFileService');
        /** @var PostResourceService $PostResourceService */
        $PostResourceService = ClassRegistry::init('PostResourceService');

        $addedFiles = [];

        try {
            foreach ($files as $file) {
                if (isset($file['file_uuid'])) {
                    /** @var UploadedFile $uploadedFile */
                    $uploadedFiles = $UploadService->getBuffers($userId, $teamId, [$file['file_uuid']]);

                    //Save attached files
                    foreach ($uploadedFiles as $uploadedFile) {

                        /** @var AttachedFileEntity $attachedFile */
                        $attachedFile = $AttachedFileService->add($userId, $teamId, $uploadedFile,
                            AttachedModelType::TYPE_MODEL_POST());

                        $addedFiles[] = $attachedFile['id'];

                        $postResourceType = $PostResourceService->getPostResourceTypeFromAttachedFileType($attachedFile['file_type']);
                        if ($isDraft) {
                            $PostResourceService->addResourceDraft(
                                $postId,
                                $postResourceType,
                                $attachedFile['id'],
                                $postFileIndex);
                            // Could not insert to post_files (post_id is not exists on here).
                        } else {
                            $PostResourceService->addResourcePost(
                                $postId,
                                $postResourceType,
                                $attachedFile['id'],
                                $postFileIndex);
                            $PostFileService->add($postId, $attachedFile['id'], $teamId, $postFileIndex);
                        }

                        $UploadService->saveWithProcessing("AttachedFile", $attachedFile['id'], 'attached', $uploadedFile);
                    }
                } else if (isset($file['is_video'])) {
                    // VideoStream (file is already in transcode)
                    if ($isDraft) {
                        $postResource = $PostResourceService->addResourceDraft(
                            $postId,
                            Enum\Model\Post\PostResourceType::VIDEO_STREAM(),
                            $file['video_stream_id'],
                            $postFileIndex);
                    } else {
                        $PostResourceService->addResourcePost(
                            $postId,
                            Enum\Model\Post\PostResourceType::VIDEO_STREAM(),
                            $file['video_stream_id'],
                            $postFileIndex);
                    }
                }
                $postFileIndex++;
            }
        } catch (Exception $e) {
            //If any error happened, remove uploaded file
            foreach ($addedFiles as $id) {
                $UploadService->deleteAsset('AttachedFile', $id);
            }
            throw $e;
        }

        return true;
    }

    /**
     * Edit a post body
     *
     * @param array $newBody
     * @param int   $postId
     * @param int   $userId
     * @param int   $teamId
     * @param array $resources
     *
     * @return PostEntity Updated post
     * @throws Exception
     */
    public function editPost(array $newBody, int $postId, int $userId, int $teamId, array $resources): PostEntity
    {
        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');

        if (!$Post->exists($postId)) {
            throw new GlException\GoalousNotFoundException(__("This post doesn't exist."));
        }
        try {
            $this->TransactionManager->begin();

            $newData = [
                'body'      => '"' . $newBody['body'] . '"',
                'site_info' => !empty($newBody['site_info']) ? "'" . addslashes(json_encode($newBody['site_info'])) . "'" : null,
                'modified'  => REQUEST_TIMESTAMP
            ];

            if (!$Post->updateAll($newData, ['Post.id' => $postId])) {
                throw new RuntimeException("Failed to update post");
            }
            $deletedPosts = $this->findDeletedResourcesInPost($postId, $resources);

            if (!empty($deletedPosts)) {
                /** @var PostResourceService $PostResourceService */
                $PostResourceService = ClassRegistry::init('PostResourceService');
                $PostResourceService->deleteResources(Hash::extract($deletedPosts, '{n}.id'));
            }
            $newResources = $this->filterNewResources($postId, $resources);

            if (!empty($newResources)) {
                /** @var PostResource $PostResource */
                $PostResource = ClassRegistry::init('PostResource');
                $this->saveFiles($postId, $userId, $teamId, $newResources, false, $PostResource->findMaxResourceOrderOfPost($postId) + 1);
            }
            $this->TransactionManager->commit();
        } catch (Exception $e) {
            $this->TransactionManager->rollback();
            GoalousLog::error("Failed to edit post $postId");
            throw $e;
        }
        /** @var PostEntity $result */
        $result = $Post->getEntity($postId);
        return $result;
    }

    /**
     * Check whether the user can view the several posts
     *
     * @param int $userId
     * @param int $postsIds
     *
     * @return bool
     * @throws Exception
     */
    public function checkUserAccessToMultiplePost(int $userId, array $postsIds): bool
    {
        /** @var Circle $Circle */
        $Circle = ClassRegistry::init('Circle');

        $options = [
            'conditions' => [
                'Circle.del_flg'  => false,
                'CircleMember.id' => null
            ],
            'fields'     => [
                'PostShareCircle.circle_id',
                'PostShareCircle.post_id',
            ],
            'joins'      => [
                [
                    'type'       => 'INNER',
                    'conditions' => [
                        'Circle.id = PostShareCircle.circle_id',
                        'PostShareCircle.post_id' => $postsIds,
                        'PostShareCircle.del_flg' => false,
                        'Circle.public_flg'       => false,
                    ],
                    'table'      => 'post_share_circles',
                    'alias'      => 'PostShareCircle',
                ],
                [
                    'type'       => 'LEFT',
                    'conditions' => [
                        'Circle.id = CircleMember.circle_id',
                        'CircleMember.user_id' => $userId,
                        'CircleMember.del_flg' => false,
                    ],
                    'table'      => 'circle_members',
                    'alias'      => 'CircleMember',
                ]
            ]
        ];

        $noPermissionPosts = $Circle->find('all', $options);
        $noPermissionPosts = Hash::extract($noPermissionPosts, '{n}.PostShareCircle');
        if (!empty($noPermissionPosts)) {
            GoalousLog::info('No permission posts', $noPermissionPosts);
            throw new GlException\GoalousNotFoundException(__("You don't have permission to access this post"));
        }

        return true;
    }

    /**
     * Find resources newly added during post edit
     *
     * @param int   $postId
     * @param array $resources
     *
     * @return array
     */
    private function filterNewResources($postId, array $resources): array
    {
        /** @var PostResource $PostResource */
        $PostResource = ClassRegistry::init('PostResource');
        $currentPostResources = $PostResource->getAllPostResources($postId);

        return array_filter($resources, function ($resource) use ($currentPostResources) {
            if (array_key_exists('is_video', $resource)) {
                // Check about given resources are already set to post.
                foreach ($currentPostResources as $currentPostResource) {
                    if ($currentPostResource["resource_type"] === Enum\Model\Post\PostResourceType::VIDEO_STREAM
                        && $currentPostResource["resource_id"] === (int)$resource["video_stream_id"]) {
                        return false;
                    }
                }
                return true;
            }
            return array_key_exists('file_uuid', $resource);
        });
    }

    /**
     * Find resources removed during post edit
     *
     * @param int   $postId
     * @param array $resources Existing resources
     *                         ['id' => 1, 'file_type' => 1]
     *
     * @return PostResource[]
     */
    private function findDeletedResourcesInPost(int $postId, array $resources): array
    {
        if (empty($resources)) {
            /** @var PostResource $PostResource */
            $PostResource = ClassRegistry::init('PostResource');
            return $PostResource->getAllPostResources($postId);
        }

        $groupedResource = [];

        /** @var PostResource $PostResource */
        $PostResource = ClassRegistry::init('PostResource');
        foreach ($resources as $resource) {
            if (isset($resource['is_video']) && $resource['is_video']) {
                $groupedResource[Enum\Model\Post\PostResourceType::VIDEO_STREAM][] = $resource['video_stream_id'];
            }
            if (!array_key_exists('resource_type', $resource)) continue;
            $groupedResource[$resource['resource_type']][] = $resource['id'];
        }

        return $PostResource->findDeletedPostResourcesInPost($postId, $groupedResource);
    }

}

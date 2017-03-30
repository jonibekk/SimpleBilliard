<?php
App::uses('Post', 'Model');
App::uses('PostRead', 'Model');
App::uses('PostFile', 'Model');
App::uses('PostShareUser', 'Model');
App::uses('Comment', 'Model');
App::uses('CommentFile', 'Model');
App::uses('CommentRead', 'Model');
App::uses('Topic', 'Model');
App::uses('TopicMember', 'Model');
App::uses('Message', 'Model');
App::uses('TopicSearchKeyword', 'Model');
App::import('Service', 'MessageService');

class MessageDataMigration0329 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'message_data_migration_0329';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = [
        'up'   => [
        ],
        'down' => [
        ],
    ];

    /**
     * Before migration callback
     *
     * @param string $direction Direction of migration process (up or down)
     *
     * @return bool Should process continue
     */
    public function before($direction)
    {
        return true;
    }

    /**
     * After migration callback
     *
     * @param string $direction Direction of migration process (up or down)
     *
     * @return bool Should process continue
     */
    public function after($direction)
    {
        // if down, skip data migration
        if ($direction == 'down') {
            return true;
        }

        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');
        $Post->cacheQueries = false;
        // 対象トピックのIDを全て取得(タイプがメッセージ)
        $postIds = $Post->findWithoutTeamId('list', ['conditions' => ['type' => $Post::TYPE_MESSAGE]]);

        try {
            foreach ($postIds as &$postId) {
                // トピック作成、1stメッセージ作成、メッセージ添付ファイル保存
                $topic = $this->createTopicAndFirstMessage($postId);
                $topicId = $topic['id'];
                $teamId = $topic['team_id'];
                // 作成されたトピックのトピックメンバーをpost_share_usersとトピック作成者を元に作成(post_share_usersにはトピック作成者が含まれていない為)
                $this->createTopicMembers($postId, $topic);
                // commentsテーブルを元にメッセージを生成しmessagesテーブルに保存([like]は絵文字に置き換える)
                // commentsのデータは大量にある可能性が高いので、メモリ消費を抑える為に1件ずつ処理する
                $this->createMessages($postId, $topicId, $teamId);
                // 最新メッセージを参照し、そのid,dateをtopicsテーブルlatest_message_id,latest_message_datetimeに反映
                $latestMessage = $this->getLatestMessage($topicId);
                $this->updateLatestMessage($latestMessage, $topicId);
                // comment_readsテーブルを参照し、最新メッセージの既読ユーザを特定し、topic_membersテーブルを更新
                // commentが一件もない場合は、post_readsテーブルから既読ユーザを特定する。
                $this->updateReadMembers($topicId, $latestMessage['id'], $postId);
                unset($topic);
                unset($topicId);
                unset($teamId);
                unset($latestMessage);
//                debug(memory_get_usage() / 1024 / 1024);
            }
            // トピック検索テーブルのレコード生成
            $this->createTopicSearchKeywords();

        } catch (Exception $e) {
            // transaction rollback
            CakeLog::error($e->getMessage());
            CakeLog::error($e->getTraceAsString());

            // if return false, it will be paused to wait input.. So, exit
            exit(1);
        }
//        debug(memory_get_usage() / 1024 / 1024);

        return true;
    }

    /**
     * return topic data
     *
     * @param $postId
     *
     * @return mixed
     */
    function createTopicAndFirstMessage($postId)
    {
        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');
        $Post->cacheQueries = false;
        /** @var PostFile $PostFile */
        $PostFile = ClassRegistry::init('PostFile');
        $PostFile->cacheQueries = false;
        /** @var PostShareUser $PostShareUser */
        $Topic = ClassRegistry::init('Topic');
        $Topic->cacheQueries = false;
        /** @var Message $Message */
        $Message = ClassRegistry::init('Message');
        $Message->cacheQueries = false;
        /** @var MessageFile $MessageFile */
        $MessageFile = ClassRegistry::init('MessageFile');
        $MessageFile->cacheQueries = false;

        // トピックの元になるpostsデータを取得
        $post = $Post->getById($postId);
        $teamId = $post['team_id'];

        // - Postのデータを元にトピックを作成
        $Topic->create();
        $topic = $Topic->save([
            'creator_user_id' => $post['user_id'],
            'team_id'         => $teamId,
            'modified'        => $post['modified'],
            'created'         => $post['created'],
        ], false);
        $topic = Hash::extract($topic, 'Topic');
        $topicId = $Topic->getLastInsertID();

        // 添付ファイル情報の取得
        $postFiles = $PostFile->findWithoutTeamId('all', ['conditions' => ['post_id' => $postId]]);
        $postFiles = Hash::extract($postFiles, '{n}.PostFile');

        // 1件目のメッセージを保存(旧バージョンはpostsテーブルに１件目のメッセージが保存されている)
        $Message->create();
        $Message->save([
            'sender_user_id'      => $post['user_id'],
            'topic_id'            => $topicId,
            'body'                => $post['body'],
            'team_id'             => $teamId,
            'attached_file_count' => count($postFiles),
            'modified'            => $post['modified'],
            'created'             => $post['created'],
        ], false);
        $messageId = $Message->getLastInsertID();

        // 1件目のメッセージの添付ファイル関連データを保存
        foreach ($postFiles as &$postFile) {
            $postFile['message_id'] = $messageId;
            $postFile['topic_id'] = $topicId;
            unset($postFile['post_id']);
            unset($postFile['id']);
        }
        $MessageFile->bulkInsert($postFiles);

        return $topic;
    }

    function createTopicMembers($postId, $topic)
    {
        /** @var PostShareUser $PostShareUser */
        $PostShareUser = ClassRegistry::init('PostShareUser');
        $PostShareUser->cacheQueries = false;
        /** @var TopicMember $TopicMember */
        $TopicMember = ClassRegistry::init('TopicMember');
        $TopicMember->cacheQueries = false;

        $postShareUsers = $PostShareUser->findWithoutTeamId('all', [
            'conditions' => ['post_id' => $postId],
            'fields'     => ['user_id', 'created'],
        ]);
        $postShareUsers = Hash::extract($postShareUsers, '{n}.PostShareUser');
        array_unshift($postShareUsers, ['user_id' => $topic['creator_user_id'], 'created' => $topic['created']]);

        $topicMembersData = [];
        foreach ($postShareUsers as $postShareUser) {
            $topicMembersData[] = [
                'user_id'  => $postShareUser['user_id'],
                'topic_id' => $topic['id'],
                'team_id'  => $topic['team_id'],
                'created'  => $postShareUser['created'],
            ];
        }
        $TopicMember->bulkInsert($topicMembersData);
    }

    /**
     * commentsテーブルを元にメッセージを生成しmessagesテーブルに保存([like]は絵文字に置き換える)
     * commentsのデータは大量にある可能性が高いので、メモリ消費を抑える為に1件ずつ処理する
     *
     * @param $postId
     * @param $topicId
     * @param $teamId
     */
    function createMessages($postId, $topicId, $teamId)
    {
        /** @var Comment $Comment */
        $Comment = ClassRegistry::init('Comment');
        $Comment->cacheQueries = false;
        /** @var Message $Message */
        $Message = ClassRegistry::init('Message');
        $Message->cacheQueries = false;
        /** @var MessageFile $MessageFile */
        $MessageFile = ClassRegistry::init('MessageFile');
        $MessageFile->cacheQueries = false;

        $commentIds = $Comment->findWithoutTeamId('list', [
            'conditions' => ['post_id' => $postId],
            'fields'     => ['id'],
        ]);
        foreach ($commentIds as &$commentId) {
            // commentとその添付ファイルからメッセージを作成&保存
            $comment = $Comment->findWithoutTeamId('first', [
                'conditions' => ['Comment.id' => $commentId],
                'contain'    => ['CommentFile']
            ]);
            $files = Hash::extract($comment, 'CommentFile');
            $comment = Hash::extract($comment, 'Comment');
            $Message->create();
            $Message->save([
                'sender_user_id'      => $comment['user_id'],
                'topic_id'            => $topicId,
                'body'                => $comment['body'] == "[like]" ? MessageService::CHAR_EMOJI_LIKE : $comment['body'],
                'team_id'             => $teamId,
                'attached_file_count' => count($files),
                'modified'            => $comment['modified'],
                'created'             => $comment['created'],
            ], false);
            $messageId = $Message->getLastInsertID();
            if (!empty($files)) {
                // 添付ファイルの保存
                foreach ($files as &$file) {
                    $file['message_id'] = $messageId;
                    $file['topic_id'] = $topicId;
                    unset($file['comment_id']);
                    unset($file['id']);
                }
                $MessageFile->bulkInsert($files);
            }
            unset($comment);
            unset($files);
        }
    }

    function getLatestMessage($topicId)
    {
        /** @var Message $Message */
        $Message = ClassRegistry::init('Message');
        $Message->cacheQueries = false;
        $latestMessage = $Message->findWithoutTeamId('first', [
            'conditions' => ['topic_id' => $topicId, 'type' => Message::TYPE_NORMAL],
            'order'      => ['id' => 'desc']
        ]);
        $latestMessage = Hash::extract($latestMessage, 'Message');
        return $latestMessage;
    }

    function updateLatestMessage($message, $topicId)
    {
        /** @var Topic $Topic */
        $Topic = ClassRegistry::init('Topic');
        $Topic->cacheQueries = false;
        $Topic->save([
            'id'                      => $topicId,
            'latest_message_id'       => $message['id'],
            'latest_message_datetime' => $message['created'],
        ], false);
    }

    function updateReadMembers($topicId, $messageId, $postId)
    {
        /** @var Comment $Comment */
        $Comment = ClassRegistry::init('Comment');
        $Comment->cacheQueries = false;
        /** @var CommentRead $CommentRead */
        $CommentRead = ClassRegistry::init('CommentRead');
        $CommentRead->cacheQueries = false;
        /** @var  PostRead $PostRead */
        $PostRead = ClassRegistry::init('PostRead');
        $PostRead->cacheQueries = false;
        /** @var TopicMember $TopicMember */
        $TopicMember = ClassRegistry::init('TopicMember');
        $TopicMember->cacheQueries = false;

        $latestComment = $Comment->findWithoutTeamId('first', [
            'conditions' => ['post_id' => $postId],
            'fields'     => ['id'],
            'order'      => ['id' => 'desc']
        ]);
        $latestCommentId = Hash::extract($latestComment, 'Comment.id');
        if ($latestCommentId) {
            $readUids = $CommentRead->findWithoutTeamId('list', [
                'conditions' => ['comment_id' => $latestCommentId],
                'fields'     => ['user_id']
            ]);
        } else {
            $readUids = $PostRead->findWithoutTeamId('list', [
                'conditions' => ['post_id' => $postId],
                'fields'     => ['user_id']
            ]);
        }
        $TopicMember->updateAll(
            ['TopicMember.last_read_message_id' => $messageId,],
            ['TopicMember.topic_id' => $topicId, 'TopicMember.user_id' => $readUids]
        );
    }

    function createTopicSearchKeywords()
    {
        /** @var TopicSearchKeyword $TopicSearchKeyword */
        $TopicSearchKeyword = ClassRegistry::init('TopicSearchKeyword');
        $TopicSearchKeyword->cacheQueries = false;
        $query = <<<SQL
INSERT INTO topic_search_keywords (topic_id, team_id, keywords)
SELECT
  tp.id AS topic_id,
  tp.team_id,
  CONCAT(
      '\n',
      GROUP_CONCAT(DISTINCT (u.last_name) SEPARATOR '\n'),
      '\n',
      GROUP_CONCAT(DISTINCT (u.first_name) SEPARATOR '\n'),
      '\n',
      ifnull(GROUP_CONCAT(DISTINCT (ln.last_name) SEPARATOR '\n'), ""),
      '\n',
      ifnull(GROUP_CONCAT(DISTINCT (ln.first_name) SEPARATOR '\n'), "")
  )     AS keywords
FROM
  topics tp
  INNER JOIN topic_members tpm ON tp.id = tpm.topic_id
                                  AND tpm.del_flg = 0
  INNER JOIN team_members tm ON tpm.user_id = tm.user_id
                                AND tpm.team_id = tm.team_id
                                AND tm.active_flg = 1
                                AND tm.del_flg = 0
  INNER JOIN users u ON tm.user_id = u.id
                        AND u.active_flg = 1
                        AND u.del_flg = 0
  LEFT JOIN local_names ln ON u.id = ln.user_id
WHERE tp.del_flg = 0
GROUP BY tp.id
SQL;
        $TopicSearchKeyword->query($query);
        return true;
    }
}

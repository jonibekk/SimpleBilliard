<?php

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

        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');
        /** @var  PostRead $PostRead */
        $PostRead = ClassRegistry::init('PostRead');
        /** @var PostFile $PostFile */
        $PostFile = ClassRegistry::init('PostFile');
        /** @var PostShareUser $PostShareUser */
        $PostShareUser = ClassRegistry::init('PostShareUser');
        /** @var Comment $Comment */
        $Comment = ClassRegistry::init('Comment');
        /** @var CommentFile $CommentFile */
        $CommentFile = ClassRegistry::init('CommentFile');
        /** @var CommentRead $CommentRead */
        $CommentRead = ClassRegistry::init('CommentRead');
        /** @var Topic $Topic */
        $Topic = ClassRegistry::init('Topic');
        /** @var TopicMember $TopicMember */
        $TopicMember = ClassRegistry::init('TopicMember');
        /** @var Message $Message */
        $Message = ClassRegistry::init('Message');
        /** @var TopicSearchKeyword $TopicSearchKeyword */
        $TopicSearchKeyword = ClassRegistry::init('TopicSearchKeyword');

        // 対象トピックのIDを全て取得
        $postList = $Post->findWithoutTeamId('list', ['conditions' => ['type' => $Post::TYPE_MESSAGE]]);

        try {
            foreach ($postList as $postId) {
                // with post files
                $post = $Post->getById($postId);
                $teamId = $post['team_id'];
                // - postsテーブルのメッセージタイプのデータを元にトピック作成
                $Topic->create();
                $Topic->save([
                    'creator_user_id' => $post['user_id'],
                    'team_id'         => $teamId,
                    'modified'        => $post['modified'],
                    'created'         => $post['created'],
                ], false);
                $topicId = $Topic->getLastInsertID();

                // 1件目のメッセージを保存(旧バージョンはpostsテーブルに１件目のメッセージが保存されている)
                $Message->create();
                $Message->save([
                    'sender_user_id' => $post['user_id'],
                    'topic_id'       => $topicId,
                    'body'           => $post['body'],
                    'team_id'        => $teamId,
                    'modified'       => $post['modified'],
                    'created'        => $post['created'],
                ], false);

                // 1件目のメッセージの添付ファイル関連データを保存
                $postFiles = $PostFile->find('all', ['conditions' => ['post_id' => $postId]]);

                // - 作成されたトピックのトピックメンバーをpost_share_usersを元に作成
                $userIds = $PostShareUser->findWithoutTeamId('list', [
                    'conditions' => ['post_id' => $postId],
                    'fields'     => ['user_id'],
                ]);
                $topicMembersData = [];
                foreach ($userIds as $uid) {
                    $topicMembersData[] = [
                        'user_id'  => $uid,
                        'topic_id' => $topicId,
                        'team_id'  => $teamId,
                    ];
                }
                $TopicMember->bulkInsert($topicMembersData);

                // - commentsテーブルを元にメッセージを生成しmessagesテーブルに保存([like]は絵文字に置き換える)

                // - 最新メッセージを参照し、そのid,dateをtopicsテーブルlatest_message_id,latest_message_datetimeに反映

                // - comment_readsテーブルを参照し、最新メッセージの既読ユーザを特定し、topic_membersテーブルを更新

                // - post_files, comment_filesテーブルを元にmessage_filesテーブルに挿入する。attached_filesテーブルは変更の必要なし。

                // - 添付ファイル数をmessagesテーブルに反映

                // - トピック検索テーブルのレコード生成

            }

        } catch (Exception $e) {
            // transaction rollback
            CakeLog::error($e->getMessage());

            // if return false, it will be paused to wait input.. So, exit
            exit(1);
        }

        return true;
    }
}

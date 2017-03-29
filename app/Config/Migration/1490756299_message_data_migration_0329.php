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

        // transaction start
        $Post->begin();
        // 対象トピックのIDを全て取得
        $postList = $Post->findWithoutTeamId('list', ['conditions' => ['type' => $Post::TYPE_MESSAGE]]);

        foreach ($postList as $postId) {
            // - postsテーブルのメッセージタイプのデータを元にトピック作成
            $post = $Post->getById($postId);
            $Post->create();
            $Post->save([
                'creator_user_id' => $post['user_id'],
                'team_id'         => $post['team_id'],
                'modified'        => $post['modified'],
                'created'         => $post['created'],
            ]);

            // - 作成されたトピックのトピックメンバーをpost_share_usersを元に作成
            $userIds = $PostShareUser->findWithoutTeamId('list', [
                'conditions' => ['post_id' => $postId],
                'fields'     => ['user_id'],
            ]);

            // - 最新メッセージを参照し、そのid,dateをtopicsテーブルlatest_message_id,latest_message_datetimeに反映

            // - comment_readsテーブルを参照し、最新メッセージの既読ユーザを特定し、topic_membersテーブルを更新

            // - postsテーブルのbodyを1件目のメッセージとし、2件目以降をcommentsテーブルを元にメッセージを生成しmessagesテーブルに保存

            // - post_files, comment_filesテーブルを元にmessage_filesテーブルに挿入する。attached_filesテーブルは変更の必要なし。

            // - 添付ファイル数をmessagesテーブルに反映

            // - トピック検索テーブルのレコード生成

        }

        // transaction commit
        $Post->commit();

        return true;
    }
}

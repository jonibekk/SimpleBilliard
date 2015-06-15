<?php

/**
 * PostShell
 *
 * @property Post $Post
 */
class PostShell extends AppShell
{

    public $uses = array(
        'Post'
    );

    public function startup()
    {
        parent::startup();
        $this->_setModelVariable();
    }

    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $commands = [
            'red_post_comment' => [
                'help'   => '投稿とコメントの既読処理',
                'parser' => [
                    'options' => [
                        'serialized_post_ids' => ['short' => 'p', 'help' => 'シリアライズした投稿IDの配列', 'required' => true,],
                        'user_id'             => ['short' => 'u', 'help' => 'ユーザID', 'required' => true,],
                        'team_id'             => ['short' => 't', 'help' => 'チームID', 'required' => true,],
                        'only_one'            => ['short' => 'o', 'help' => '単独投稿か？', 'required' => true,],
                    ]
                ]
            ],
        ];
        $parser->addSubcommands($commands);
        return $parser;
    }

    public function main()
    {
    }

    public function red_post_comment()
    {
        $post_ids = unserialize(base64_decode($this->params['serialized_post_ids']));
        $this->Post->PostRead->red($post_ids);
        $res = $this->Post->getForRed($post_ids, $this->params['only_one']);
        //コメントを既読に
        if (!empty($res)) {
            $comment_list = Hash::extract($res, '{n}.Comment.{n}.id');
            $this->Post->Comment->CommentRead->red($comment_list);
        }

    }

    function _setModelVariable()
    {
        $uid = $this->params['user_id'];
        $team_id = $this->params['team_id'];

        $this->Post->my_uid =
        $this->Post->PostRead->my_uid =
        $this->Post->Comment->CommentRead->my_uid =
            $uid;
        $this->Post->current_team_id =
        $this->Post->PostRead->current_team_id =
        $this->Post->Comment->CommentRead->current_team_id =
            $team_id;
    }

}

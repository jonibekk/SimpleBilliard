<?php
App::uses('CakeSchema', 'Model');

/**
 * ダミーデータ登録用スクリプト
 * Created by PhpStorm.
 * User: bigplants
 * Date: 5/16/14
 * Time: 2:49 PM
 *
 * @property User $User
 * @property Team $Team
 */
class FixUnreadPostUnconsistencyShell extends AppShell
{

    public $uses = [
        'CacheUnreadCirclePost',
        'CircleMember',
        'PostRead'
    ];

    function startup()
    {
        parent::startup();
    }

    function main()
    {
        // get inconsistent data: existing on cache_unread_circle_posts but already read by user
        $condition = [
            'table' => 'cache_unread_circle_posts',
            'alias' => 'CacheUnreadCirclePost',
            'conditions' => [
                'CacheUnreadCirclePost.del_flg' => false,
            ],
            'joins' => [
                [
                    'table'      => 'post_reads',
                    'alias'      => 'PostRead',
                    'type'       => 'INNER',
                    'conditions' => [
                        'PostRead.post_id = CacheUnreadCirclePost.post_id',
                        'PostRead.user_id = CacheUnreadCirclePost.user_id',
                        'PostRead.del_flg' => false,
                    ]
                ],
            ]
        ];
        $res = $this->CacheUnreadCirclePost->find('all', $condition);
        var_dump($res);
        
        // get unique user_id and circle_id
        $target = [];
        $ids  = [];
        foreach ($res as $cachePost){
            $user_id = $cachePost['CacheUnreadCirclePost']['user_id'];
            $circle_id = $cachePost['CacheUnreadCirclePost']['circle_id'];
            if (!isset($target[$user_id])){
                $target[$user_id] = array();
            } 
            if (!isset($target[$user_id][$circle_id])){
                $target[$user_id][$circle_id] = 0;
            }
            $target[$user_id][$circle_id]++;
            $ids[] = $cachePost['CacheUnreadCirclePost']['id'];
        }
        var_dump($target);

        // delete inconsistent data in cache_unread_circle_posts
        $condition = [
             'id' => $ids,
         ];

        $this->CacheUnreadCirclePost->deleteAll($condition);

        // maintain unread_count in circle_members to be correct
        foreach ($target as $user_id => $info){
            foreach ($info as $circle_id=> $count){
                $conditions = [

                    'table' => 'cache_unread_circle_posts',
                    'alias' => 'CacheUnreadCirclePost',
                    'conditions' => [
                        'CacheUnreadCirclePost.del_flg' => false,
                        'CacheUnreadCirclePost.circle_id' => $circle_id,
                        'CacheUnreadCirclePost.user_id' => $user_id,
                    ],
                ];
                $correct = $this->CacheUnreadCirclePost->find('count', $conditions);

                $conditions = [

                    'table' => 'circle_members',
                    'alias' => 'CircleMember',
                    'conditions' => [
                        'CircleMember.del_flg' => false,
                        'CircleMember.circle_id' => $circle_id,
                        'CircleMember.user_id' => $user_id,
                    ],
                ];
                $res = $this->CircleMember->find('first', $conditions);
                $res['CircleMember']['unread_count'] = $correct;
                $this->CircleMember->save($res);
            }
        }
    }

}

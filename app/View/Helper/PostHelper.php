<?php
App::uses('AppHelper', 'View/Helper');
App::uses('SessionHelper', 'View/Helper');

/**
 * Created by PhpStorm.
 * User: saekis
 * Date: 16/03/16
 * Time: 18:08
 *
 * @property SessionHelper $Session
 */
class PostHelper extends AppHelper
{
    var $helpers = [
        'Session'
    ];

    /**
     * @param $json_site_info
     *
     * @return string
     */
    function extractOgpUrl($json_site_info)
    {
        if (!$json_site_info) {
            return '';
        }

        $site_info = json_decode($json_site_info);
        if (viaIsSet($site_info->url)) {
            return $site_info->url;
        }

        return '';
    }

    /**
     * フィードの投稿内にフォローとコラボのボタン表示ができるかのチェック
     * 以下の場合は表示不可
     * - KR達成、ゴール作成以外の投稿
     * - 本人がゴールリーダの場合
     * - 本人が投稿主の場合
     * - ゴール期限が今期より前の場合
     * - 完了済みのゴール
     *
     * @param array $post
     * @param array $goal
     * @param array $current_term
     *
     * @return bool
     */
    function isDisplayableGoalButtons(array $post, array $goal, array $current_term)
    {
        //KR達成、ゴール作成以外はボタン表示しない
        if (!in_array($post['type'], [Post::TYPE_KR_COMPLETE, Post::TYPE_CREATE_GOAL])) {
            return false;
        }
        //本人がゴールリーダの場合はボタン表示しない
        if ($goal['user_id'] == $this->Session->read('Auth.User.id')) {
            return false;
        }
        //本人が投稿主の場合はボタン表示しない
        if ($post['user_id'] == $this->Session->read('Auth.User.id')) {
            return false;
        }
        //ゴール期限が今期より前の場合はボタン表示しない
        if ($goal['end_date'] <= $current_term['start_date']) {
            return false;
        }
        //完了済みのゴールはボタン表示しない
        if (!is_null($goal['completed'])) {
            return false;
        }
        return true;
    }

    function getPostTimeBefore(array $posts, array $currentCircle, array $currentTeam)
    {
        $firstPost = $posts[0] ?? null;
        // circle_feed ページの場合
        if (!empty($currentCircle) && $firstPost) {
            $postTimeBefore = $firstPost['Post']['modified'];
        } // ホーム画面の場合
        elseif (!empty($currentTeam) && $firstPost) {
            $postTimeBefore = $firstPost['Post']['created'];
        } else {
            $postTimeBefore = null;
        }
        return $postTimeBefore;
    }

    function getOldestPostTime(array $currentCircle, array $currentTeam): int
    {
        // circle_feed ページの場合
        if (!empty($currentCircle)) {
            $oldestPostTime = $currentCircle['Circle']['created'];
        } // ホーム画面の場合
        elseif (!empty($currentTeam)) {
            $oldestPostTime = $currentTeam['Team']['created'];
        } else {
            $oldestPostTime = 0;
        }
        return $oldestPostTime;
    }

}

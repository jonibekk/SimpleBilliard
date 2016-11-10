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
     * @param array $post
     * @param array $current_term
     *
     * @return bool
     */
    function isDisplayableGoalButtons(array $post, array $current_term)
    {
        if (!Hash::get($post, 'Goal.id')) {
            return false;
        }
        //KR達成、ゴール作成以外はボタン表示しない
        if (!in_array($post['Post']['type'], [Post::TYPE_KR_COMPLETE, Post::TYPE_CREATE_GOAL])) {
            return false;
        }
        //本人がゴールリーダの場合はボタン表示しない
        if ($post['Goal']['user_id'] == $this->Session->read('Auth.User.id')) {
            return false;
        }
        //本人が投稿主の場合はボタン表示しない
        if ($post['Post']['user_id'] == $this->Session->read('Auth.User.id')) {
            return false;
        }
        //ゴール期限が今期より前の場合はボタン表示しない
        if ($post['Goal']['end_date'] <= $current_term['start_date']) {
            return false;
        }
        //完了済みのゴールはボタン表示しない
        if (!is_null($post['Goal']['completed'])) {
            return false;
        }
        //KR達成の場合、KRの期限が過去の場合はボタン表示しない
        if ($post['Post']['type'] == Post::TYPE_KR_COMPLETE &&
            REQUEST_TIMESTAMP > $post['KeyResult']['end_date']
        ) {
            return false;
        }

        return true;
    }

}

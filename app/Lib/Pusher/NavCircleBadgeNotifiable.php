<?php
App::import('Lib/Pusher', 'BaseNotifiable');
/**
 * Created by PhpStorm.
 * User: stephen
 * Date: 19/03/20
 * Time: 10:38
 */

class NavCircleBadgeNotifiable extends BaseNotifiable
{
    protected function setChannelName(int $teamId = 0)
    {
        $this->channelName =  "team_" . $teamId;
    }

    protected function setEventName()
    {
        $this->eventName =  'nav.circle_badge';
    }

    protected function setData(int $circleId = 0, PostEntity $post = null)
    {
        $this->data = [
            'circle_id' => (string)$circleId,
            'post_id' => (string)$post['id']
        ];
    }

    public function build(PostEntity $post = null, int $circleId = 0)
    {
        $this->setChannelName($post['team_id']);
        $this->setEventName();
        $this->setData($circleId, $post);
    }

}